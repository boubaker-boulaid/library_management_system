<?php

use App\Livewire\BooksFilter;
use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use App\Models\Type;
use Livewire\Livewire;

beforeEach(function () {
    $this->english = Language::factory()->create(['name' => 'English']);
    $this->french = Language::factory()->create(['name' => 'French']);
    $this->roman = Type::factory()->create(['name' => 'Roman']);
    $this->comic = Type::factory()->create(['name' => 'Comic']);
    $this->fiction = Category::factory()->create(['name' => 'Fiction']);

    $this->a = Book::factory()->create([
        'designation' => 'Alpha', 'prix' => 30, 'language_id' => $this->english->id, 'type_id' => $this->roman->id,
    ]);
    $this->a->categories()->attach($this->fiction);

    $this->b = Book::factory()->create([
        'designation' => 'Beta', 'prix' => 10, 'language_id' => $this->french->id, 'type_id' => $this->comic->id,
    ]);
});

test('the books page renders the filter', function () {
    $this->get(route('books'))->assertOk()->assertSeeLivewire(BooksFilter::class);
});

test('books can be filtered by category, language and type', function () {
    Livewire::test(BooksFilter::class)
        ->set('category', (string) $this->fiction->id)
        ->assertViewHas('booksCount', 1)
        ->assertSee('Alpha')
        ->assertDontSee('Beta');

    Livewire::test(BooksFilter::class)
        ->set('language', (string) $this->french->id)
        ->assertViewHas('booksCount', 1)
        ->assertSee('Beta');

    Livewire::test(BooksFilter::class)
        ->set('type', (string) $this->roman->id)
        ->assertViewHas('booksCount', 1)
        ->assertSee('Alpha');
});

test('books can be sorted by price', function () {
    Livewire::test(BooksFilter::class)
        ->set('sort', 'sort_price_low_high')
        ->assertSeeInOrder(['Beta', 'Alpha']);
});

test('an unknown sort value falls back to the default order', function () {
    Livewire::test(BooksFilter::class)
        ->set('sort', 'prix; drop table books')
        ->assertOk()
        ->assertViewHas('booksCount', 2);
});

test('filters are read from the query string', function () {
    Livewire::withQueryParams(['type' => (string) $this->comic->id])
        ->test(BooksFilter::class)
        ->assertViewHas('booksCount', 1)
        ->assertSee('Beta');
});
