<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reference data needed in every environment. These seeders are idempotent.
        $this->call([
            CategorySeeder::class,
            LanguageSeeder::class,
            TypeSeeder::class,
        ]);

        // Demo data and a known admin login must never reach production.
        // Create real admins with `php artisan users:create --admin`.
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $this->call(BookSeeder::class);

        User::factory()->create([
            'name' => 'Demo Admin',
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
