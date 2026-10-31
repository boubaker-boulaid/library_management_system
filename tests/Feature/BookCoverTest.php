<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    $this->language = Language::create(['name' => 'English']);
    $this->type = Type::create(['name' => 'Text']);
    $this->category = Category::create(['name' => 'Fiction']);
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->payload = [
        'designation' => 'Cover Book',
        'auteur' => 'Jane Doe',
        'prix' => 10,
        'type_id' => $this->type->id,
        'language_id' => $this->language->id,
        'categories' => [$this->category->id],
    ];
});

test('an uploaded cover is stored on the public disk under a generated name', function () {
    $this->actingAs($this->admin)->post(route('book.store'), [
        ...$this->payload,
        'cover' => UploadedFile::fake()->image('../../evil name.jpg'),
    ])->assertRedirect(route('book.index'));

    $book = Book::where('designation', 'Cover Book')->firstOrFail();

    expect($book->cover)->toStartWith('covers/')
        ->not->toContain('evil');
    Storage::disk('public')->assertExists($book->cover);
});

test('svg covers are rejected', function () {
    $this->actingAs($this->admin)->post(route('book.store'), [
        ...$this->payload,
        'cover' => UploadedFile::fake()->create('cover.svg', 1, 'image/svg+xml'),
    ])->assertSessionHasErrors('cover');

    expect(Book::count())->toBe(0);
});

test('updating a book without a new cover keeps the existing cover', function () {
    $path = UploadedFile::fake()->image('cover.jpg')->store('covers', 'public');
    $book = Book::factory()->create(['type_id' => $this->type->id, 'cover' => $path]);

    $this->actingAs($this->admin)->put(route('book.update', $book), [
        ...$this->payload,
        'designation' => 'Renamed',
    ])->assertRedirect(route('book.show', $book));

    expect($book->fresh()->cover)->toBe($path);
    Storage::disk('public')->assertExists($path);
});

test('replacing a cover deletes the old file', function () {
    $old = UploadedFile::fake()->image('old.jpg')->store('covers', 'public');
    $book = Book::factory()->create(['type_id' => $this->type->id, 'cover' => $old]);

    $this->actingAs($this->admin)->put(route('book.update', $book), [
        ...$this->payload,
        'cover' => UploadedFile::fake()->image('new.jpg'),
    ]);

    $new = $book->fresh()->cover;
    expect($new)->not->toBe($old);
    Storage::disk('public')->assertMissing($old);
    Storage::disk('public')->assertExists($new);
});

test('deleting a book deletes its cover', function () {
    $path = UploadedFile::fake()->image('cover.jpg')->store('covers', 'public');
    $book = Book::factory()->create(['type_id' => $this->type->id, 'cover' => $path]);

    $this->actingAs($this->admin)->delete(route('book.destroy', $book));

    Storage::disk('public')->assertMissing($path);
});

test('cover url falls back to the placeholder and supports legacy covers', function () {
    expect(Book::make(['cover' => null])->cover_url)->toBe(asset('covers/no_cover.jpg'))
        ->and(Book::make(['cover' => '123_old.jpg'])->cover_url)->toBe(asset('covers/123_old.jpg'))
        ->and(Book::make(['cover' => 'covers/abc.jpg'])->cover_url)->toBe(Storage::disk('public')->url('covers/abc.jpg'));
});
