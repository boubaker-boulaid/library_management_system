<?php

namespace App\Providers;

use App\Events\BookCreated;
use App\Events\BookDeleted;
use App\Events\BookUpdated;
use App\Listeners\LogBookActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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

        $production = $this->app->isProduction();

        // Surface lazy loading (N+1) and mass-assignment mistakes outside production.
        Model::shouldBeStrict(! $production);

        DB::prohibitDestructiveCommands($production);

        URL::forceHttps($production);

        Password::defaults(fn () => $production
            ? Password::min(10)->letters()->mixedCase()->numbers()
            : Password::min(8));
    }
}
