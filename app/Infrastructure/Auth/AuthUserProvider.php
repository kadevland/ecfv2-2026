<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use SensitiveParameter;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Infrastructure\Database\Models\Auth\User;
use App\Infrastructure\Database\Models\Auth\UserCredential;
use App\Infrastructure\Database\Schemas\Auth\UserCredentialSchema;

/**
 * @internal
 */
final class AuthUserProvider extends EloquentUserProvider implements UserProvider
{
    public function __construct(Hasher $hasher, string $model)
    {
        parent::__construct($hasher, $model);
    }

    /**
     * Retrieve a user by their unique identifier.
     */
    public function retrieveById($identifier): ?Authenticatable
    {
        $model = $this->createModel();

        return $this->newModelQuery($model)
            ->with(User::RELATION_CREDENTIAL)
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first();
    }

    /**
     * Retrieve a user by the given credentials (email/password).
     * Also supports password reset (email only).
     *
     * @param array<string, mixed> $credentials
     * @return (Authenticatable&\Illuminate\Database\Eloquent\Model)|null
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (empty($credentials)) {
            return null;
        }

        // For password reset, we might only have email (no password)
        // For login, we need both email and password
        if (count($credentials) === 1 && str_contains(array_key_first($credentials), 'password')) {
            return null;
        }

        // Find user by email in credentials table
        $email = $credentials['email'] ?? null;
        if (!$email) {
            return null;
        }

        return $this->findUserByCredentialEmail($email);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param array<string, mixed> $credentials
     */
    public function validateCredentials(Authenticatable $user, #[SensitiveParameter] array $credentials): bool
    {
        $password = $credentials['password'] ?? null;
        if (!$password) {
            $this->waitHashTime($password);

            return false;
        }

        if (!$user instanceof User) {
            $this->waitHashTime($password);

            return false;
        }

        // Load credential if not already loaded
        if (!$user->relationLoaded(User::RELATION_CREDENTIAL)) {
            $user->load(User::RELATION_CREDENTIAL);
        }

        $credential = $user->credential;
        if (!$credential) {
            $this->waitHashTime($password);

            return false;
        }

        return $this->hasher->check($password, $credential->password_hash);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     */
    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        $model = $this->createModel();

        $retrievedModel = $this->newModelQuery($model)
            ->with(User::RELATION_CREDENTIAL)
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first();

        if (!$retrievedModel) {
            return null;
        }

        $rememberToken = $retrievedModel->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token)
            ? $retrievedModel
            : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     */
    public function updateRememberToken(Authenticatable $user, $token): void
    {
        if ($user instanceof User) {
            $user->setRememberToken($token);
            $user->save();
        }
    }

    /**
     * Find user by credential email with eager loaded user relation.
     *
     * @return (Authenticatable&\Illuminate\Database\Eloquent\Model)|null
     */
    protected function findUserByCredentialEmail(string $email): ?Authenticatable
    {
        $credential = UserCredential::with(UserCredential::RELATION_USER)
            ->where(UserCredentialSchema::EMAIL, $email)
            ->first();

        return $credential?->user;
    }

    /**
     * Simulate the time taken to hash a password to mitigate timing attacks.
     */
    private function waitHashTime(?string $password): void
    {
        // Use the actual password provided to ensure consistent timing
        // Hash it against a dummy hash to simulate real validation time
        $dummyHash = '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG'; // bcrypt hash for 'password'
        $this->hasher->check($password ?? 'invalid_password', $dummyHash);
    }
}
