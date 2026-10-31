<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateUserInteractiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create {--admin : Give the new user the admin role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user interactively';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $data = [
            'name' => $this->ask('Name'),
            'email' => $this->ask('Email'),
            'password' => $this->secret('Password'),
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $role = $this->option('admin') ? User::ROLE_ADMIN : User::ROLE_USER;

        if (! $this->confirm("Create {$role} {$data['email']}?", true)) {
            $this->warn('Cancelled.');

            return self::SUCCESS;
        }

        $user = User::create($validator->validated());
        $user->forceFill(['role' => $role])->save();

        $this->info("User {$user->email} created (id {$user->id}, role {$role}).");

        return self::SUCCESS;
    }
}
