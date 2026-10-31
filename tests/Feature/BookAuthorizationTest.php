<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use App\Models\Type;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->language = Language::create(['name' => 'English']);
    $this->type = Type::create(['name' => 'Text']);
    $this->category = Category::create(['name' => 'Fiction']);
    $this->user = User::factory()->create();
    $this->book = Book::factory()->create([
        'language_id' => $this->language->id,
        'type_id' => $this->type->id,
    ]);
    $this->payload = [
        'designation' => 'Unauthorized Book',
        'auteur' => 'Nobody',
        'prix' => 10,
        'type_id' => $this->type->id,
        'categories' => [$this->category->id],
    ];
});

test('guests can browse books', function () {
    $this->get(route('book.index'))->assertOk();
    $this->get(route('book.show', $this->book))->assertOk();
});

test('guests are redirected to login for book management', function () {
    $this->get(route('book.create'))->assertRedirect(route('login'));
    $this->post(route('book.store'), $this->payload)->assertRedirect(route('login'));
});

test('regular users cannot manage books', function () {
    $this->actingAs($this->user);

    $this->get(route('book.create'))->assertForbidden();
    $this->post(route('book.store'), $this->payload)->assertForbidden();
    $this->get(route('book.edit', $this->book))->assertForbidden();
    $this->put(route('book.update', $this->book), $this->payload)->assertForbidden();
    $this->delete(route('book.destroy', $this->book))->assertForbidden();

    expect(Book::count())->toBe(1);
});

test('regular users do not see management links', function () {
    $this->actingAs($this->user)
        ->get(route('book.index'))
        ->assertDontSee(route('book.create'))
        ->assertDontSee(route('book.edit', $this->book));
});

test('admins see management links', function () {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]))
        ->get(route('book.index'))
        ->assertSee(route('book.create'))
        ->assertSee(route('book.edit', $this->book));
});

test('regular users cannot manage books through the API', function () {
    Sanctum::actingAs($this->user);

    $this->postJson('/api/books', $this->payload)->assertForbidden();
    $this->putJson("/api/books/{$this->book->id}", $this->payload)->assertForbidden();
    $this->deleteJson("/api/books/{$this->book->id}")->assertForbidden();

    expect(Book::count())->toBe(1);
});
