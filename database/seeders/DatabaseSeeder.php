<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Book;
use App\Models\Language;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $categories = Category::all();

        // Book::chunk(100, function($books) use ($categories) {
        //     foreach ($books as $book){
        //         $book->categories()->syncWithoutDetaching($categories->random());
        //     }
        // });

        $languages = Language::all();

        Book::chunk(100, function($books) use ($languages) {
            foreach($books as $book) {
                $book->update([
                    'language_id' => $languages->random()->id,
                ]);
            }
        });

        
        // $this->call([
        //     CategorySeeder::class,
        // ]);
    }
}
