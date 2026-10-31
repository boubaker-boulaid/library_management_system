<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, config('app.available_locales'), true), 400);

    session()->put('locale', $locale);

    return redirect()->back();
})->name('language.switch');

// original routes
Route::middleware(['auth'])->group(function () {
    Route::resource('book', BookController::class)->except(['index', 'show']);
    Route::post('/sendBook/{book}', [BookController::class, 'mailBookToUser'])
        ->middleware('throttle:3,1')
        ->name('sendBook');

});

Route::resource('book', BookController::class)->only(['index', 'show']);

Route::get('/books', function () {
    return view('books');
})->name('books');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// breeze routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
