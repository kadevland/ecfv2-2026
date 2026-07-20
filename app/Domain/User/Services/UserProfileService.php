<?php

declare(strict_types=1);

namespace App\Domain\User\Services;

use App\Infrastructure\Database\Models\Auth\User;

final class UserProfileService
{
    /**
     * Get user profile data based on user type
     *
     * @return array<string, mixed>|null
     */
    public function getUserProfile(User $user): ?array
    {
        if ($user->isClient() && $user->clientProfile) {
            return $this->getClientProfile($user);
        }

        if (($user->isEmployee() || $user->isAdmin()) && $user->employeeProfile) {
            return $this->getEmployeeProfile($user);
        }

        return null;
    }

    /**
     * Get user profile data for API responses
     *
     * @return array<string, mixed>|null
     */
    public function getUserProfileForApi(User $user): ?array
    {
        if ($user->isClient() && $user->clientProfile) {
            return [
                'nom'       => $user->clientProfile->nom,
                'prenom'    => $user->clientProfile->prenom,
                'email'     => $user->clientProfile->email, // @phpstan-ignore-line
                'telephone' => $user->clientProfile->telephone,
                'full_name' => $user->clientProfile->full_name, // @phpstan-ignore-line
                'age'       => $user->clientProfile->age, // @phpstan-ignore-line
            ];
        }

        if (($user->isEmployee() || $user->isAdmin()) && $user->employeeProfile) {
            return [
                'nom'                 => $user->employeeProfile->nom, // @phpstan-ignore-line
                'prenom'              => $user->employeeProfile->prenom, // @phpstan-ignore-line
                'email_professionnel' => $user->employeeProfile->email_professionnel, // @phpstan-ignore-line
                'poste'               => $user->employeeProfile->poste->value, // @phpstan-ignore-line
                'departement'         => $user->employeeProfile->departement?->value, // @phpstan-ignore-line
                'full_name'           => $user->employeeProfile->full_name, // @phpstan-ignore-line
                'anciennete'          => $user->employeeProfile->anciennete, // @phpstan-ignore-line
            ];
        }

        return null;
    }

    /**
     * Get client profile for web dashboard
     *
     * @return array<string, mixed>
     */
    private function getClientProfile(User $user): array
    {
        return [
            'type'      => 'client',
            'nom'       => $user->clientProfile->nom,
            'prenom'    => $user->clientProfile->prenom,
            'email'     => $user->clientProfile->email, // @phpstan-ignore-line
            'full_name' => $user->clientProfile->full_name, // @phpstan-ignore-line
        ];
    }

    /**
     * Get employee/admin profile for web dashboard
     *
     * @return array<string, mixed>
     */
    private function getEmployeeProfile(User $user): array
    {
        return [
            'type'        => $user->isAdmin() ? 'admin' : 'employee',
            'nom'         => $user->employeeProfile->nom, // @phpstan-ignore-line
            'prenom'      => $user->employeeProfile->prenom, // @phpstan-ignore-line
            'poste'       => $user->employeeProfile->poste->value, // @phpstan-ignore-line
            'departement' => $user->employeeProfile->departement?->value, // @phpstan-ignore-line
            'full_name'   => $user->employeeProfile->full_name, // @phpstan-ignore-line
        ];
    }
}
