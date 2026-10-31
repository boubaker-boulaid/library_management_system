<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('users:count', function () {
    $count = User::count();
    $this->info("The users count is {$count}");
})->purpose('display the users count');

Schedule::command('sanctum:prune-expired --hours=24')->daily();
