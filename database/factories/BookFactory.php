<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
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
            'type_id' => \App\Models\Type::inRandomOrder()->first()?->id ?? 1,
            'language_id' => \App\Models\Language::inRandomOrder()->first()?->id ?? 1,
        ];
    }
}
