<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeleteUserInteractiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a user interactively';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Label by id and email: names are not unique.
        $users = User::orderBy('id')->get()
            ->mapWithKeys(fn (User $user) => [$user->id => "#{$user->id} {$user->name} <{$user->email}>"]);

        if ($users->isEmpty()) {
            $this->warn('No users to delete.');

            return self::SUCCESS;
        }

        $label = $this->choice('Which user do you want to delete?', $users->values()->all());
        $userId = $users->search($label);

        if ($this->confirm("Delete {$label}?")) {
            User::destroy($userId);
            $this->info('User deleted.');
        } else {
            $this->warn('Cancelled.');
        }

        return self::SUCCESS;
    }
}
