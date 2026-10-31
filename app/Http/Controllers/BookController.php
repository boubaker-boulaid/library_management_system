<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Mail\SendBook;
use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use App\Models\Type;
use App\Services\BookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BookController extends Controller
{
    public function __construct(private BookService $books) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $books = Book::query()
            ->when($request->string('search')->trim()->value(), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('designation', 'like', '%'.$search.'%')
                        ->orWhere('auteur', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Book::class);

        return view('books.create', $this->formOptions());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): RedirectResponse
    {
        $this->books->create($request->validated(), $request->file('cover'));

        return redirect()->route('book.index')->with('success', __('books.added_success'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book): View
    {
        $book->load(['categories', 'language', 'type']);

        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book): View
    {
        Gate::authorize('update', $book);

        $book->load(['categories', 'language', 'type']);

        return view('books.edit', ['book' => $book, ...$this->formOptions()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $this->books->update($book, $request->validated(), $request->file('cover'));

        return redirect()->route('book.show', $book)->with('success', __('books.updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): RedirectResponse
    {
        Gate::authorize('delete', $book);

        $this->books->delete($book);

        return redirect()->route('book.index')->with('success', __('books.deleted_success'));
    }

    /**
     * Send the book details to the authenticated user by e-mail.
     */
    public function mailBookToUser(Request $request, Book $book): RedirectResponse
    {
        Mail::to($request->user())->queue(new SendBook($book));

        return redirect()->back()->with('success', __('books.email_sent'));
    }

    /**
     * @return array{categories: \Illuminate\Support\Collection, languages: \Illuminate\Support\Collection, types: \Illuminate\Support\Collection}
     */
    private function formOptions(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'languages' => Language::orderBy('name')->get(),
            'types' => Type::orderBy('name')->get(),
        ];
    }
}
