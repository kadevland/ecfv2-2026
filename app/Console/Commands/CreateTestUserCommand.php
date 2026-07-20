<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use App\Enums\UserType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Infrastructure\Database\Models\Auth\User;
use App\Infrastructure\Database\Models\Auth\UserCredential;

class CreateTestUserCommand extends Command
{
    protected $signature = 'user:create-test-marie';

    protected $description = 'Create marie.martin@gmail.com test user';

    public function handle(): int
    {
        try {
            $uuid = '550e8400-e29b-41d4-a716-446655440001'; // UUID fixe pour Marie

            // Check if user already exists
            $existingUser = User::where('id', $uuid)->first();
            if ($existingUser) {
                $this->info('User marie.martin@gmail.com already exists');

                return 0;
            }

            // Create user
            $user = User::create([
                'id'        => $uuid,
                'type'      => UserType::CLIENT,
                'is_active' => true,
            ]);

            // Create credentials
            UserCredential::create([
                'user_db_id'    => $user->db_id,
                'user_uuid'     => $user->id,
                'email'         => 'marie.martin@gmail.com',
                'password_hash' => Hash::make('Client123!@#'),
            ]);

            $this->info('✅ User marie.martin@gmail.com created successfully');

            return 0;

        } catch (Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());

            return 1;
        }
    }
}
