<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Book;
use App\Models\Language;
use App\Models\Type;
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
        $this->call([
            LanguageSeeder::class,
            TypeSeeder::class,
        ]);

        $languages = Language::all();
        $types = Type::all();

        Book::chunk(100, function($books) use ($languages, $types) {
            foreach($books as $book) {
                $book->update([
                    'language_id' => $languages->random()->id,
                    'type_id' => $types->random()->id,
                ]);
            }
        });
    }
}
