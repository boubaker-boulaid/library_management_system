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
        // 1. Seed categories, languages, and types first
        $this->call([
            CategorySeeder::class,
            LanguageSeeder::class,
            TypeSeeder::class,
        ]);

        // 2. Seed books
        $this->call([
            BookSeeder::class,
        ]);

        // 3. Create a default admin/user account for testing
        \App\Models\User::factory()->create([
            'name' => 'Mohamed',
            'email' => 'mohamed@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
