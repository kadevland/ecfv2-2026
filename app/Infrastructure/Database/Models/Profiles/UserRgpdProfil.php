<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Profiles;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\Profiles\UserRgpdProfilFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Profiles\UserRgpdProfilSchema;

/**
 * @property string $nom_substitution
 * @property string $prenom_substitution
 * @property \Illuminate\Support\Carbon|null $date_suppression_effective
 */
final class UserRgpdProfil extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = UserRgpdProfilSchema::FULL_TABLE;

    protected $primaryKey = UserRgpdProfilSchema::ID;

    protected $fillable = [
        UserRgpdProfilSchema::USER_ID_ORIGINAL,
        UserRgpdProfilSchema::NOM_SUBSTITUTION,
        UserRgpdProfilSchema::PRENOM_SUBSTITUTION,
        UserRgpdProfilSchema::EMAIL_SUBSTITUTION,
        UserRgpdProfilSchema::DATE_SUPPRESSION_DEMANDEE,
        UserRgpdProfilSchema::DATE_SUPPRESSION_EFFECTIVE,
        UserRgpdProfilSchema::RAISON_SUPPRESSION,
        UserRgpdProfilSchema::OPERATEUR_SUPPRESSION,
        UserRgpdProfilSchema::COMMENTAIRE_INTERNE,
        UserRgpdProfilSchema::AVAIT_RESERVATIONS,
        UserRgpdProfilSchema::NOMBRE_RESERVATIONS_HISTORIQUE,
    ];

    protected $casts = [
        UserRgpdProfilSchema::DATE_SUPPRESSION_DEMANDEE      => 'datetime',
        UserRgpdProfilSchema::DATE_SUPPRESSION_EFFECTIVE     => 'datetime',
        UserRgpdProfilSchema::AVAIT_RESERVATIONS             => 'boolean',
        UserRgpdProfilSchema::NOMBRE_RESERVATIONS_HISTORIQUE => 'integer',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->prenom_substitution . ' ' . $this->nom_substitution;
    }

    public function isRecentlyDeleted(): bool
    {
        return $this->date_suppression_effective &&
               $this->date_suppression_effective->gt(Carbon::now()->subDays(30));
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): UserRgpdProfilFactory
    {
        return UserRgpdProfilFactory::new();
    }
}
