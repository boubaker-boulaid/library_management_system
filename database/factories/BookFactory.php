<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Language;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'designation' => $this->faker->unique()->sentence(3),
            'description' => $this->faker->paragraph(),
            'editeur' => $this->faker->company(),
            'prix' => $this->faker->randomFloat(2, 0, 900),
            'annee' => $this->faker->date(),
            'auteur' => $this->faker->name(),
            'cover' => 'no_cover.jpg',
            // Reuse seeded lookup rows when they exist, otherwise create one.
            'type_id' => fn () => Type::inRandomOrder()->first() ?? Type::factory(),
            'language_id' => fn () => Language::inRandomOrder()->first() ?? Language::factory(),
        ];
    }
}
