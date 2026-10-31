<?php

use App\Models\Book;
use App\Models\Language;
use App\Models\Type;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->language = Language::create(['name' => 'English']);
    $this->type = Type::create(['name' => 'Text']);
    $this->user = User::factory()->create([
        'email' => 'api_user@example.com',
        'password' => bcrypt('password123'),
    ]);
});

test('user can log in via API and receive a token', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'api_user@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'role'],
        ]);
});

test('unauthenticated user cannot access books API resource', function () {
    $response = $this->getJson('/api/books');
    $response->assertStatus(401);
});

test('authenticated user with token can access books API resource', function () {
    Sanctum::actingAs($this->user);

    Book::factory()->create([
        'language_id' => $this->language->id,
        'type_id' => $this->type->id,
    ]);

    $response = $this->getJson('/api/books');

    $response->assertStatus(200);
});

test('user can log out via API', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => __('auth.logged_out')]);
});
