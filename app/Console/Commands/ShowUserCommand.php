<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ShowUserCommand extends Command
{
    /**
     * Fields that are safe to print. Secrets such as the password hash are excluded.
     */
    private const FIELDS = ['id', 'name', 'email', 'role', 'email_verified_at', 'created_at', 'updated_at'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:show {id} {--field=name : The field to display}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show a user field';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $field = $this->option('field');

        if (! in_array($field, self::FIELDS, true)) {
            $this->error('Unknown field. Allowed: '.implode(', ', self::FIELDS));

            return self::FAILURE;
        }

        $user = User::find($this->argument('id'));

        if (! $user) {
            $this->warn('No user found.');

            return self::FAILURE;
        }

        $this->info("{$field} of user {$user->id}: {$user->{$field}}");

        return self::SUCCESS;
    }
}
