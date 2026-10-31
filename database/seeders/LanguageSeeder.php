<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            'Spanish',
            'French',
            'German',
            'Arabic',
            'English'
        ];

        foreach($languages as $language) {
            Language::firstOrCreate(['name' => $language]);
        }
    }
}
