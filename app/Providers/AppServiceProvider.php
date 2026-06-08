<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\BookCreated;
use App\Events\BookUpdated;
use App\Events\BookDeleted;
use App\Listeners\LogBookActivity;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(BookCreated::class, LogBookActivity::class);
        Event::listen(BookUpdated::class, LogBookActivity::class);
        Event::listen(BookDeleted::class, LogBookActivity::class);
    }
}
