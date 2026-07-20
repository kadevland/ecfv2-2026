<?php

declare(strict_types=1);

namespace App\Observers;

use Log;
use Exception;
use App\Infrastructure\Database\Models\Auth\User;
use App\Infrastructure\Database\Models\Profiles\UserProfil;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

final class UserObserver
{
    /**
     * Handle the User "created" event.
     * Synchronize type in UserProfil when User is created (if profil exists).
     */
    public function created(User $user): void
    {
        $this->syncTypeInProfil($user);
    }

    /**
     * Handle the User "updated" event.
     * Synchronize type in UserProfil when User.type is updated.
     */
    public function updated(User $user): void
    {
        // Vérifier si le champ 'type' a été modifié
        if ($user->isDirty('type')) {
            $this->syncTypeInProfil($user);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // UserProfil sera supprimé par cascade (FK constraint)
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $this->syncTypeInProfil($user);
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        // UserProfil sera supprimé par cascade (FK constraint)
    }

    /**
     * Synchronize the type field from User to UserProfil for CQRS optimization.
     */
    private function syncTypeInProfil(User $user): void
    {
        try {
            // Mettre à jour le profil s'il existe
            UserProfil::where(UserProfilSchema::USER_KEY, $user->db_id)
                ->update([UserProfilSchema::TYPE => $user->type->value]);
        } catch (Exception $e) {
            // Log l'erreur mais ne pas faire échouer l'opération sur User
            Log::warning('Failed to sync user type to profil', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
