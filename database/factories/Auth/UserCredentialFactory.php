<?php

declare(strict_types=1);

namespace Database\Factories\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Infrastructure\Database\Schemas\Auth\UserCredentialSchema;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Database\Models\Auth\UserCredential>
 */
final class UserCredentialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Infrastructure\Database\Models\Auth\UserCredential>
     */
    protected $model = \App\Infrastructure\Database\Models\Auth\UserCredential::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            UserCredentialSchema::EMAIL             => $this->faker->unique()->safeEmail(),
            UserCredentialSchema::PASSWORD_HASH     => Hash::make('password'),
            UserCredentialSchema::EMAIL_VERIFIED_AT => $this->faker->boolean(80) ? now() : null,
            UserCredentialSchema::REMEMBER_TOKEN    => null,
        ];
    }

    /**
     * Create verified email credentials
     */
    public function verified(): static
    {
        return $this->state(fn () => [
            UserCredentialSchema::EMAIL_VERIFIED_AT => now(),
        ]);
    }

    /**
     * Create unverified email credentials
     */
    public function unverified(): static
    {
        return $this->state(fn () => [
            UserCredentialSchema::EMAIL_VERIFIED_AT => null,
        ]);
    }

    /**
     * Create credentials with specific password
     */
    public function withPassword(string $password): static
    {
        return $this->state(fn () => [
            UserCredentialSchema::PASSWORD_HASH => Hash::make($password),
        ]);
    }
}
