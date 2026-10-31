<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Mail\SendBook;
use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use App\Models\Type;
use App\Services\BookCoverStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $books = Book::query()
                                ->when($request->search, function ($query, $search) {
                                    $query->where('designation', 'like', '%'.$search.'%')
                                        ->orWhere('auteur', 'like', '%'.$search.'%');
                                })
                                ->latest()
                                ->paginate(8)->withQueryString();
                                
            Log::info('Books fetched successfully');

            return view('books.index', compact('books'));
        } catch (\Exception $e) {

            //Log the error
            Log::error('error fetching the books:' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', 'error : ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Book::class);

        $categories = Category::latest()->get();
        $languages = Language::latest()->get();
        $types = Type::latest()->get();
        return view('books.create', compact('categories', 'languages', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request, BookCoverStorage $covers)
    {
        DB::beginTransaction();

        try {

            $validated = $request->validated();

            if ($request->hasFile('cover')) {
                $validated['cover'] = $covers->store($request->file('cover'));
            }

            $book = Book::create($validated);
            
            if (!empty($validated['categories'])){
                $book->categories()->sync($validated['categories']);
            }

            event(new \App\Events\BookCreated($book));

            DB::commit();
            //Log the success
            Log::info('Book added successfully: ' . $validated['designation']);

            return redirect()->route('book.index')->with('success', __('books.added_success'));
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($validated['cover'])) {
                $covers->delete($validated['cover']);
            }

            //Log the error
            Log::error('Error adding book: ' . $e->getMessage());

            return redirect()->back()->withInput()
                ->with('error', __('books.added_error'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load('categories');
        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        Gate::authorize('update', $book);

        $categories = Category::latest()->get();
        $book->load('categories');
        $languages = Language::latest()->get();
        $book->load('language');
        $types = Type::latest()->get();
        $book->load('type');
        return view('books.edit', compact('book', 'categories', 'languages', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book, BookCoverStorage $covers)
    {
        $oldCover = $book->cover;

        DB::beginTransaction();
        try {

            $validated = $request->validated();

            if ($request->hasFile('cover')) {
                $validated['cover'] = $covers->store($request->file('cover'));
            }

            $book->update($validated);

            if (!empty($validated['categories'])){
                $book->categories()->sync($validated['categories']);
            }

            event(new \App\Events\BookUpdated($book));

            DB::commit();

            // Only remove the previous cover once the new one is saved in the database.
            if (isset($validated['cover'])) {
                $covers->delete($oldCover);
            }

            //Log success
            Log::info('Book updated successfully: ' . $validated['designation']);

            return redirect()->route('book.show', $book)->with('success', __('books.updated_success'));
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($validated['cover'])) {
                $covers->delete($validated['cover']);
            }

            //Log error 
            Log::error('error updating the book ' . $book->designation . ': ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', __('books.updated_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book, BookCoverStorage $covers)
    {
        Gate::authorize('delete', $book);

        try {

            event(new \App\Events\BookDeleted($book));

            $book->delete();

            $covers->delete($book->cover);

            //Log the success
            Log::info('Book removed successfully: ' . $book->designation);

            return redirect()->route('book.index')->with('success', __('books.deleted_success'));
        } catch (\Exception $e) {

            //Log error
            Log::error('error removing the book ' . $book->designation . ': ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', __('books.deleted_error'));
        }
    }
    public function mailBookToUser (Book $book)
    {
        $user = Auth::user();

        Mail::to($user)->send(new SendBook($book));

        return redirect()->back()->with('success', __('books.email_sent'));
    }
}
