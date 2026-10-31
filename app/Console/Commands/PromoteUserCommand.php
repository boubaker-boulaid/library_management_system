<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:promote {email} {--demote : Remove the admin role instead}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant or remove the admin role for a user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No user found with that email.');

            return self::FAILURE;
        }

        $role = $this->option('demote') ? User::ROLE_USER : User::ROLE_ADMIN;
        $user->forceFill(['role' => $role])->save();

        $this->info("{$user->email} is now {$role}.");

        return self::SUCCESS;
    }
}
