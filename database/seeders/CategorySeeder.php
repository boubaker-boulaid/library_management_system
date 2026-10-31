<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Action',
            'Horreur',
            'Mystere',
            'Fiction',
            'Romance',
            'Science-Fiction',
            'Fantastique',
            'Classique',
            'Biographie',
            'Histoire',
            'Poesie',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }

    }
}
