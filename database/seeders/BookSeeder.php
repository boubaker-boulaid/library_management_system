<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        Book::factory()
            ->count(40)
            ->create()
            ->each(function ($book) use ($categories) {
                if ($categories->isNotEmpty()) {
                    $book->categories()->attach(
                        $categories->random(rand(1, 3))->pluck('id')->toArray()
                    );
                }
            });
    }
}
