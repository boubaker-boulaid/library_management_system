<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $users = User::orderBy('id')->get(['id', 'name', 'email', 'role']);

        if ($users->isEmpty()) {
            $this->warn('No users found.');

            return self::SUCCESS;
        }

        $this->table(['ID', 'Name', 'Email', 'Role'], $users->map->only(['id', 'name', 'email', 'role']));

        return self::SUCCESS;
    }
}
