<?php

declare(strict_types=1);

namespace Database\Factories\Auth;

use App\Enums\TokenType;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Infrastructure\Database\Schemas\Auth\UserAccessTokenSchema;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Database\Models\Auth\UserAccessToken>
 */
final class UserAccessTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Infrastructure\Database\Models\Auth\UserAccessToken>
     */
    protected $model = \App\Infrastructure\Database\Models\Auth\UserAccessToken::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tokenType = $this->faker->randomElement(TokenType::cases());

        return [
            UserAccessTokenSchema::TOKEN_TYPE   => $tokenType,
            UserAccessTokenSchema::TOKEN        => hash('sha256', Str::random(64)),
            UserAccessTokenSchema::LAST_USED_AT => $this->faker->boolean(70) ? $this->faker->dateTimeBetween('-1 week') : null,
            UserAccessTokenSchema::EXPIRES_AT   => now()->addDays($tokenType->defaultExpiration()),
        ];
    }

    /**
     * Create a mobile token
     */
    public function mobile(): static
    {
        return $this->state(fn () => [
            UserAccessTokenSchema::TOKEN_TYPE => TokenType::MOBILE,
            UserAccessTokenSchema::EXPIRES_AT => now()->addDays(TokenType::MOBILE->defaultExpiration()),
        ]);
    }

    /**
     * Create a desktop token
     */
    public function desktop(): static
    {
        return $this->state(fn () => [
            UserAccessTokenSchema::TOKEN_TYPE => TokenType::DESKTOP,
            UserAccessTokenSchema::EXPIRES_AT => now()->addDays(TokenType::DESKTOP->defaultExpiration()),
        ]);
    }

    /**
     * Create an API token
     */
    public function api(): static
    {
        return $this->state(fn () => [
            UserAccessTokenSchema::TOKEN_TYPE => TokenType::API,
            UserAccessTokenSchema::EXPIRES_AT => now()->addDays(TokenType::API->defaultExpiration()),
        ]);
    }

    /**
     * Create an expired token
     */
    public function expired(): static
    {
        return $this->state(fn () => [
            UserAccessTokenSchema::EXPIRES_AT   => now()->subDay(),
            UserAccessTokenSchema::LAST_USED_AT => $this->faker->dateTimeBetween('-1 week', '-1 day'),
        ]);
    }

    /**
     * Create a recently used token
     */
    public function recentlyUsed(): static
    {
        return $this->state(fn () => [
            UserAccessTokenSchema::LAST_USED_AT => now()->subMinutes($this->faker->numberBetween(1, 60)),
        ]);
    }
}
