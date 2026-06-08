<?php

use App\Models\Book;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Language;
use App\Models\Type;
use App\Models\Category;
use App\Events\BookCreated;
use App\Events\BookUpdated;
use App\Events\BookDeleted;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->language = Language::create(['name' => 'English']);
    $this->type = Type::create(['name' => 'Text']);
    $this->category = Category::create(['name' => 'Fiction']);
    $this->user = User::factory()->create(['role' => 'admin']);
});

test('admin can create a book and it triggers created event and log', function () {
    Event::fake([BookCreated::class]);

    $this->actingAs($this->user);

    $response = $this->post(route('book.store'), [
        'designation' => 'Test Book Title',
        'description' => 'Test book description.',
        'prix' => 19.99,
        'auteur' => 'John Doe',
        'type_id' => $this->type->id,
        'language_id' => $this->language->id,
        'annee' => '2026-06-08',
        'editeur' => 'Publisher X',
        'categories' => [$this->category->id],
    ]);

    $response->assertRedirect(route('book.index'));

    $this->assertDatabaseHas('books', [
        'designation' => 'Test Book Title',
        'auteur' => 'John Doe',
    ]);

    Event::assertDispatched(BookCreated::class);
});

test('creating a book writes to custom database log', function () {
    $this->actingAs($this->user);

    $this->post(route('book.store'), [
        'designation' => 'Log Book Title',
        'description' => 'Test book description.',
        'prix' => 12.99,
        'auteur' => 'Jane Doe',
        'type_id' => $this->type->id,
        'language_id' => $this->language->id,
        'annee' => '2026-06-08',
        'editeur' => 'Publisher Y',
        'categories' => [$this->category->id],
    ]);

    $book = Book::where('designation', 'Log Book Title')->first();

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->user->id,
        'book_id' => $book->id,
        'action' => 'created',
        'book_title' => 'Log Book Title',
    ]);
});

test('updating a book writes to custom database log', function () {
    $book = Book::factory()->create([
        'language_id' => $this->language->id,
        'type_id' => $this->type->id,
        'designation' => 'Old Title',
    ]);

    $this->actingAs($this->user);

    $this->put(route('book.update', $book), [
        'designation' => 'New Title',
        'description' => $book->description,
        'prix' => $book->prix,
        'auteur' => $book->auteur,
        'type_id' => $this->type->id,
        'language_id' => $this->language->id,
        'annee' => $book->annee,
        'editeur' => $book->editeur,
        'categories' => [$this->category->id],
    ]);

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->user->id,
        'book_id' => $book->id,
        'action' => 'updated',
        'book_title' => 'New Title',
    ]);
});

test('deleting a book writes to custom database log', function () {
    $book = Book::factory()->create([
        'language_id' => $this->language->id,
        'type_id' => $this->type->id,
        'designation' => 'To Be Deleted',
    ]);

    $this->actingAs($this->user);

    $this->delete(route('book.destroy', $book));

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->user->id,
        'action' => 'deleted',
        'book_title' => 'To Be Deleted',
    ]);
});
