<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use App\Enums\UserType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Infrastructure\Database\Models\Auth\User;
use App\Infrastructure\Database\Models\Auth\UserCredential;
use App\Infrastructure\Database\Models\Profiles\EmployeeProfile;

final class CreateAdminUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'user:create-admin
                            {--email= : Admin email address}
                            {--password= : Admin password}
                            {--name= : Admin full name}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new admin user for production';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email    = $this->option('email') ?? $this->ask('Admin email');
        $password = $this->option('password') ?? $this->secret('Admin password');
        $name     = $this->option('name') ?? $this->ask('Admin full name');

        if (!$email || !$password || !$name) {
            $this->error('Email, password and name are required');

            return self::FAILURE;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email format');

            return self::FAILURE;
        }

        // Check if user already exists
        if (UserCredential::where('email', $email)->exists()) {
            $this->error('User with this email already exists');

            return self::FAILURE;
        }

        try {
            // Create user
            $user = User::create([
                'type'      => UserType::ADMIN,
                'is_active' => true,
            ]);

            // Create credential
            UserCredential::create([
                'user_uuid'     => $user->id,
                'email'         => $email,
                'password_hash' => Hash::make($password),
            ]);

            // Create employee profile
            EmployeeProfile::create([
                'user_uuid'   => $user->id,
                'nom'         => explode(' ', $name)[1] ?? $name,
                'prenom'      => explode(' ', $name)[0],
                'poste'       => 'Administrateur',
                'departement' => 'Direction',
            ]);

            $this->info('Admin user created successfully!');
            $this->info("Email: {$email}");
            $this->info("UUID: {$user->id}");

            return self::SUCCESS;

        } catch (Exception $e) {
            $this->error("Failed to create admin user: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
