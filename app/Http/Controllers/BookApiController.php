<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class BookApiController extends Controller
{
    private const RELATIONS = ['categories', 'language', 'type'];

    public function __construct(private BookService $books) {}

    /**
     * Display a paginated listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        return BookResource::collection(
            Book::with(self::RELATIONS)->latest()->paginate($perPage)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): BookResource
    {
        $book = $this->books->create($request->validated(), $request->file('cover'));

        return new BookResource($book->load(self::RELATIONS));
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book): BookResource
    {
        return new BookResource($book->load(self::RELATIONS));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $book = $this->books->update($book, $request->validated(), $request->file('cover'));

        return new BookResource($book->load(self::RELATIONS));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): Response
    {
        Gate::authorize('delete', $book);

        $this->books->delete($book);

        return response()->noContent();
    }
}
