<?php

declare(strict_types=1);

namespace App\Providers;

use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Auth;
use App\Infrastructure\Auth\AuthUserProvider;
use App\Infrastructure\Database\Models\Auth\UserAccessToken;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

final class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::provider('appUserAuth', function ($app, array $config) {
            return new AuthUserProvider(
                $app['hash'],
                $config['model']
            );
        });

        // Configure Sanctum to use our UserAccessToken model
        // @phpstan-ignore-next-line - UserAccessToken implements same interface as PersonalAccessToken
        Sanctum::usePersonalAccessTokenModel(UserAccessToken::class);
    }
}
