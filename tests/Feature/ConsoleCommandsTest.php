<?php

use App\Models\Type;
use App\Models\User;

test('users:create creates an admin with a hashed password', function () {
    $this->artisan('users:create', ['--admin' => true])
        ->expectsQuestion('Name', 'Admin')
        ->expectsQuestion('Email', 'admin@library.test')
        ->expectsQuestion('Password', 'secret-password')
        ->expectsConfirmation('Create admin admin@library.test?', 'yes')
        ->assertSuccessful();

    $user = User::where('email', 'admin@library.test')->firstOrFail();

    expect($user->isAdmin())->toBeTrue()
        ->and($user->password)->not->toBe('secret-password');
});

test('users:create rejects invalid input', function () {
    User::factory()->create(['email' => 'taken@library.test']);

    $this->artisan('users:create')
        ->expectsQuestion('Name', 'Someone')
        ->expectsQuestion('Email', 'taken@library.test')
        ->expectsQuestion('Password', 'short')
        ->assertFailed();

    expect(User::count())->toBe(1);
});

test('users:promote toggles the admin role', function () {
    $user = User::factory()->create();

    $this->artisan('users:promote', ['email' => $user->email])->assertSuccessful();
    expect($user->fresh()->isAdmin())->toBeTrue();

    $this->artisan('users:promote', ['email' => $user->email, '--demote' => true])->assertSuccessful();
    expect($user->fresh()->isAdmin())->toBeFalse();
});

test('users:show refuses to print secrets', function () {
    $user = User::factory()->create();

    $this->artisan('users:show', ['id' => $user->id, '--field' => 'password'])->assertFailed();
    $this->artisan('users:show', ['id' => $user->id, '--field' => 'email'])
        ->expectsOutputToContain($user->email)
        ->assertSuccessful();
});

test('the seeder does not create demo users outside local and testing', function () {
    app()->detectEnvironment(fn () => 'production');

    $this->artisan('db:seed', ['--force' => true])->assertSuccessful();

    expect(User::count())->toBe(0)
        ->and(Type::count())->toBeGreaterThan(0);
});
