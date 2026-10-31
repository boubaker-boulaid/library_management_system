<?php

use App\Models\Book;
use App\Models\Language;
use App\Models\Type;
use Illuminate\Database\QueryException;

beforeEach(function () {
    $this->language = Language::create(['name' => 'English']);
    $this->type = Type::create(['name' => 'Text']);
});

test('deleting a language keeps its books', function () {
    $book = Book::factory()->create([
        'language_id' => $this->language->id,
        'type_id' => $this->type->id,
    ]);

    $this->language->delete();

    expect($book->fresh())->not->toBeNull()
        ->and($book->fresh()->language_id)->toBeNull();
});

test('a type that is still used by books cannot be deleted', function () {
    Book::factory()->create([
        'language_id' => $this->language->id,
        'type_id' => $this->type->id,
    ]);

    expect(fn () => $this->type->delete())->toThrow(QueryException::class);
    expect(Book::count())->toBe(1);
});

test('a book can be stored without a description', function () {
    $book = Book::factory()->create([
        'type_id' => $this->type->id,
        'description' => null,
    ]);

    expect($book->fresh()->description)->toBeNull();
});
