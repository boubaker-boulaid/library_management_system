<?php

use App\Mail\SendBook;
use App\Models\Book;
use App\Models\Category;

test('the book mail renders the book details without loading external scripts', function () {
    $book = Book::factory()->create(['designation' => 'Mail Book', 'editeur' => 'Acme']);
    $book->categories()->attach(Category::factory()->create(['name' => 'Poesie']));

    $mail = new SendBook($book->fresh());

    $mail->assertHasSubject(__('email.subject', ['title' => 'Mail Book']));
    $mail->assertSeeInHtml('Mail Book');
    $mail->assertSeeInHtml('Poesie');
    $mail->assertSeeInHtml('Acme');
    $mail->assertSeeInHtml(route('book.show', $book));
    $mail->assertDontSeeInHtml('<script', false);
});
