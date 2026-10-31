<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use App\Models\Type;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->type = Type::factory()->create();
    $this->language = Language::factory()->create();
    $this->categories = Category::factory()->count(2)->create();
    Sanctum::actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
});

test('the index is paginated and exposes a stable resource shape', function () {
    Book::factory()->count(3)->create(['type_id' => $this->type->id]);

    $this->getJson('/api/books?per_page=2')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('meta.total', 3)
        ->assertJsonStructure(['data' => [['id', 'designation', 'auteur', 'prix', 'cover_url', 'type', 'language', 'categories']]])
        ->assertJsonMissingPath('data.0.type_id');
});

test('per_page is capped', function () {
    $this->getJson('/api/books?per_page=1000')->assertOk()->assertJsonPath('meta.per_page', 100);
});

test('a book can be created with its categories', function () {
    $response = $this->postJson('/api/books', [
        'designation' => 'API Book',
        'auteur' => 'Api Author',
        'prix' => 12.5,
        'type_id' => $this->type->id,
        'language_id' => $this->language->id,
        'categories' => $this->categories->pluck('id')->all(),
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.designation', 'API Book')
        ->assertJsonPath('data.prix', '12.50')
        ->assertJsonCount(2, 'data.categories');

    expect(Book::firstWhere('designation', 'API Book')->categories)->toHaveCount(2);
    $this->assertDatabaseHas('activity_logs', ['action' => 'created', 'book_title' => 'API Book']);
});

test('a book can be shown, updated and deleted', function () {
    $book = Book::factory()->create(['type_id' => $this->type->id]);

    $this->getJson("/api/books/{$book->id}")->assertOk()->assertJsonPath('data.id', $book->id);

    $this->putJson("/api/books/{$book->id}", [
        'designation' => 'Updated',
        'auteur' => $book->auteur,
        'prix' => 5,
        'type_id' => $this->type->id,
        'categories' => [$this->categories->first()->id],
    ])->assertOk()->assertJsonPath('data.designation', 'Updated');

    expect($book->fresh()->categories)->toHaveCount(1);

    $this->deleteJson("/api/books/{$book->id}")->assertNoContent();
    expect(Book::find($book->id))->toBeNull();
});

test('missing books return 404', function () {
    $this->getJson('/api/books/999')->assertNotFound();
});
