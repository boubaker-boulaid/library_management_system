<?php

use App\Mail\SendBook;
use App\Models\Book;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('responses include security headers', function () {
    $this->get('/')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});

test('the language can be switched to every offered locale', function (string $locale) {
    $this->from('/')->post(route('language.switch', $locale))->assertRedirect('/');

    $this->get('/')->assertSee('lang="'.$locale.'"', false);
})->with(['en', 'fr', 'ar']);

test('arabic pages are rendered right to left', function () {
    $this->post(route('language.switch', 'ar'));

    $this->get('/')->assertSee('dir="rtl"', false);
});

test('unknown locales are rejected', function () {
    $this->post(route('language.switch', 'xx'))->assertStatus(400);
});

test('sending a book by e-mail requires POST and is rate limited', function () {
    Mail::fake();

    $user = User::factory()->create();
    $book = Book::factory()->create(['type_id' => Type::create(['name' => 'Text'])->id]);

    $this->actingAs($user)->get("/sendBook/{$book->id}")->assertMethodNotAllowed();

    foreach (range(1, 3) as $attempt) {
        $this->post(route('sendBook', $book))->assertRedirect();
    }

    $this->post(route('sendBook', $book))->assertTooManyRequests();

    Mail::assertQueued(SendBook::class, 3);
});

test('api login is rate limited', function () {
    foreach (range(1, 5) as $attempt) {
        $this->postJson('/api/login', ['email' => 'a@example.com', 'password' => 'wrong'])->assertUnauthorized();
    }

    $this->postJson('/api/login', ['email' => 'a@example.com', 'password' => 'wrong'])->assertTooManyRequests();
});

test('the removed books details route no longer exists', function () {
    $this->get('/books/details')->assertNotFound();
});
