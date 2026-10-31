<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Type;
use App\Models\User;

beforeEach(function () {
    $this->type = Type::factory()->create();
    $this->category = Category::factory()->create();
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $this->valid = [
        'designation' => 'Valid Book',
        'auteur' => 'Author',
        'prix' => 10,
        'type_id' => $this->type->id,
        'categories' => [$this->category->id],
    ];
});

test('invalid book data is rejected', function (array $override, string $field) {
    $this->post(route('book.store'), [...$this->valid, ...$override])->assertSessionHasErrors($field);

    expect(Book::count())->toBe(0);
})->with([
    'missing title' => [['designation' => ''], 'designation'],
    'missing author' => [['auteur' => ''], 'auteur'],
    'zero price' => [['prix' => 0], 'prix'],
    'unknown type' => [['type_id' => 999], 'type_id'],
    'no categories' => [['categories' => []], 'categories'],
    'unknown category' => [['categories' => [999]], 'categories.0'],
    'invalid date' => [['annee' => 'not-a-date'], 'annee'],
]);

test('book titles must be unique', function () {
    Book::factory()->create(['designation' => 'Valid Book', 'type_id' => $this->type->id]);

    $this->post(route('book.store'), $this->valid)->assertSessionHasErrors('designation');
});

test('a book keeps its own title when updated', function () {
    $book = Book::factory()->create(['designation' => 'Valid Book', 'type_id' => $this->type->id]);

    $this->put(route('book.update', $book), [...$this->valid, 'prix' => 20])
        ->assertSessionHasNoErrors();

    expect($book->fresh()->prix)->toBe('20.00');
});

test('a book can be created without optional fields', function () {
    $this->post(route('book.store'), $this->valid)->assertRedirect(route('book.index'));

    expect(Book::firstWhere('designation', 'Valid Book'))
        ->description->toBeNull()
        ->language_id->toBeNull();
});

test('search matches title or author', function () {
    Book::factory()->create(['designation' => 'Dune', 'auteur' => 'Herbert', 'type_id' => $this->type->id]);
    Book::factory()->create(['designation' => 'Emma', 'auteur' => 'Austen', 'type_id' => $this->type->id]);

    $this->get(route('book.index', ['search' => 'Austen']))->assertSee('Emma')->assertDontSee('Dune');
    $this->get(route('book.index', ['search' => 'Dun']))->assertSee('Dune')->assertDontSee('Emma');
});
