<?php

declare(strict_types=1);

namespace Database\Factories\Auth;

use App\Enums\UserType;
use App\Infrastructure\Database\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Models\Auth\UserCredential;
use App\Infrastructure\Database\Schemas\Auth\UserCredentialSchema;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Database\Models\Auth\User>
 */
final class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Infrastructure\Database\Models\Auth\User>
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            UserSchema::TYPE      => $this->faker->randomElement(UserType::activeTypes()),
            UserSchema::IS_ACTIVE => true,
        ];
    }

    /**
     * Configure the model factory for User.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            // Créer automatiquement les credentials associés
            UserCredential::factory()->create([
                UserCredentialSchema::USER_KEY => $user->db_id,
                UserCredentialSchema::USER_ID  => $user->id, // UUID pour compatibilité
            ]);

            // Note: Profils créés séparément par UserProfilSeeder
            // pour éviter les dépendances circulaires avec les factories
        });
    }

    /**
     * Create a client user
     */
    public function client(): static
    {
        return $this->state(fn (): array => [
            UserSchema::TYPE => UserType::CLIENT,
        ]);
    }

    /**
     * Create an employee user
     */
    public function employee(): static
    {
        return $this->state(fn (): array => [
            UserSchema::TYPE => UserType::EMPLOYEE,
        ]);
    }

    /**
     * Create an admin user
     */
    public function admin(): static
    {
        return $this->state(fn (): array => [
            UserSchema::TYPE => UserType::ADMIN,
        ]);
    }

    /**
     * Create a deleted client
     */
    public function deleted(): static
    {
        return $this->state(fn (): array => [
            UserSchema::TYPE      => UserType::CLIENT_DELETED,
            UserSchema::IS_ACTIVE => false,
        ]);
    }
}
