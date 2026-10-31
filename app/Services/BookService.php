<?php

namespace App\Services;

use App\Events\BookCreated;
use App\Events\BookDeleted;
use App\Events\BookUpdated;
use App\Models\Book;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class BookService
{
    public function __construct(private BookCoverStorage $covers) {}

    /**
     * @param  array<string, mixed>  $data  validated book attributes, including "categories"
     */
    public function create(array $data, ?UploadedFile $cover = null): Book
    {
        $attributes = Arr::except($data, ['categories', 'cover']);

        if ($cover) {
            $attributes['cover'] = $this->covers->store($cover);
        }

        try {
            return DB::transaction(function () use ($attributes, $data) {
                $book = Book::create($attributes);
                $book->categories()->sync($data['categories'] ?? []);

                BookCreated::dispatch($book);

                return $book;
            });
        } catch (Throwable $e) {
            $this->covers->delete($attributes['cover'] ?? null);

            throw $e;
        }
    }

    /**
     * @param  array<string, mixed>  $data  validated book attributes, optionally including "categories"
     */
    public function update(Book $book, array $data, ?UploadedFile $cover = null): Book
    {
        $attributes = Arr::except($data, ['categories', 'cover']);
        $oldCover = $book->cover;

        if ($cover) {
            $attributes['cover'] = $this->covers->store($cover);
        }

        try {
            DB::transaction(function () use ($book, $attributes, $data) {
                $book->update($attributes);

                if (array_key_exists('categories', $data)) {
                    $book->categories()->sync($data['categories']);
                }

                BookUpdated::dispatch($book);
            });
        } catch (Throwable $e) {
            $this->covers->delete($attributes['cover'] ?? null);

            throw $e;
        }

        // Only remove the previous cover once the new one is saved in the database.
        if ($cover) {
            $this->covers->delete($oldCover);
        }

        return $book;
    }

    public function delete(Book $book): void
    {
        DB::transaction(function () use ($book) {
            BookDeleted::dispatch($book);

            $book->delete();
        });

        $this->covers->delete($book->cover);
    }
}
