<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Employees;

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Database\Models\Auth\User;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Infrastructure\Database\Models\Cinema\Cinema;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Models\Profiles\UserProfil;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;
use App\Infrastructure\Database\Schemas\Employees\EmploiSchema;
use App\Infrastructure\Database\Schemas\Employees\ContratSchema;
use App\Infrastructure\Database\Schemas\Employees\IncidentSchema;
use App\Infrastructure\Database\Schemas\Employees\PlanningSchema;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

/**
 * @property string $uuid
 * @property int $user_profil_db_id
 * @property string $user_profil_uuid
 * @property int $cinema_db_id
 * @property string $cinema_uuid
 * @property string $titre_poste
 * @property string $description
 * @property string $categorie
 * @property string $niveau
 * @property string $type_contrat
 * @property string $temps_travail
 * @property int $salaire_min_ht_centimes
 * @property int $salaire_max_ht_centimes
 * @property string $devise
 * @property string $periodicite_salaire
 * @property array<string>|null $avantages
 * @property array<string>|null $competences_requises
 * @property array<string>|null $competences_souhaitees
 * @property array<string>|null $formations_requises
 * @property int|null $experience_minimum_mois
 * @property string|null $heure_debut_type
 * @property string|null $heure_fin_type
 * @property array<string>|null $jours_travail
 * @property bool $travail_weekend
 * @property bool $travail_feries
 * @property bool $travail_soiree
 * @property array<string>|null $responsabilites
 * @property bool $encadrement_equipe
 * @property int|null $nombre_personnes_encadrees
 * @property int|null $responsable_hierarchique_id
 * @property string $statut
 * @property bool $recrutement_ouvert
 * @property \Illuminate\Support\Carbon $date_creation_poste
 * @property \Illuminate\Support\Carbon|null $date_fermeture_poste
 * @property \Illuminate\Support\Carbon|null $date_embauche
 * @property string|null $code_poste
 * @property string|null $classification_convention
 * @property string|null $notes_rh
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read UserProfil $userProfil
 * @property-read Cinema $cinema
 * @property-read Contrat|null $contrat
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Planning> $plannings
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Incident> $incidents
 */
final class Emploi extends Model
{
    use HasFactory;

    protected $connection = EmploiSchema::CONNECTION;

    protected $table = EmploiSchema::FULL_TABLE;

    protected $primaryKey = EmploiSchema::PRIMARY_KEY; // db_id auto-increment

    protected $fillable = [
        EmploiSchema::ID,
        EmploiSchema::USER_PROFIL_KEY,
        EmploiSchema::USER_PROFIL_ID,
        EmploiSchema::CINEMA_KEY,
        EmploiSchema::CINEMA_ID,
        EmploiSchema::TITRE_POSTE,
        EmploiSchema::DESCRIPTION,
        EmploiSchema::CATEGORIE,
        EmploiSchema::NIVEAU,
        EmploiSchema::TYPE_CONTRAT,
        EmploiSchema::TEMPS_TRAVAIL,
        EmploiSchema::SALAIRE_MIN_HT_CENTIMES,
        EmploiSchema::SALAIRE_MAX_HT_CENTIMES,
        EmploiSchema::DEVISE,
        EmploiSchema::PERIODICITE_SALAIRE,
        EmploiSchema::AVANTAGES,
        EmploiSchema::COMPETENCES_REQUISES,
        EmploiSchema::COMPETENCES_SOUHAITEES,
        EmploiSchema::FORMATIONS_REQUISES,
        EmploiSchema::EXPERIENCE_MINIMUM_MOIS,
        EmploiSchema::HEURE_DEBUT_TYPE,
        EmploiSchema::HEURE_FIN_TYPE,
        EmploiSchema::JOURS_TRAVAIL,
        EmploiSchema::TRAVAIL_WEEKEND,
        EmploiSchema::TRAVAIL_FERIES,
        EmploiSchema::TRAVAIL_SOIREE,
        EmploiSchema::RESPONSABILITES,
        EmploiSchema::ENCADREMENT_EQUIPE,
        EmploiSchema::NOMBRE_PERSONNES_ENCADREES,
        EmploiSchema::RESPONSABLE_HIERARCHIQUE_ID,
        EmploiSchema::STATUT,
        EmploiSchema::RECRUTEMENT_OUVERT,
        EmploiSchema::DATE_CREATION_POSTE,
        EmploiSchema::DATE_FERMETURE_POSTE,
        EmploiSchema::CODE_POSTE,
        EmploiSchema::CLASSIFICATION_CONVENTION,
        EmploiSchema::NOTES_RH,
    ];

    /**
     * Get the user profil (employee) that owns this emploi.
     *
     * @return BelongsTo<UserProfil, $this>
     */
    public function userProfil(): BelongsTo
    {
        /** @var BelongsTo<UserProfil, $this> */
        return $this->belongsTo(UserProfil::class, EmploiSchema::USER_PROFIL_KEY, UserProfilSchema::PRIMARY_KEY);
    }

    /**
     * Get the cinema for this emploi.
     *
     * @return BelongsTo<Cinema, $this>
     */
    public function cinema(): BelongsTo
    {
        /** @var BelongsTo<Cinema, $this> */
        return $this->belongsTo(Cinema::class, EmploiSchema::CINEMA_ID, CinemaSchema::ID);
    }

    /**
     * Get the manager for this emploi.
     *
     * @return BelongsTo<User, $this>
     */
    public function manager(): BelongsTo
    {
        /** @var BelongsTo<User, $this> */
        return $this->belongsTo(User::class, EmploiSchema::MANAGER_DB_ID, UserSchema::PRIMARY_KEY);
    }

    /**
     * Get the contrat for this emploi.
     *
     * @return HasOne<Contrat, $this>
     */
    public function contrat(): HasOne
    {
        /** @var HasOne<Contrat, $this> */
        // @phpstan-ignore classConstant.notFound
        return $this->hasOne(Contrat::class, ContratSchema::EMPLOI_ID, EmploiSchema::ID);
    }

    /**
     * Get the plannings for this emploi.
     *
     * @return HasMany<Planning, $this>
     */
    public function plannings(): HasMany
    {
        /** @var HasMany<Planning, $this> */
        return $this->hasMany(Planning::class, PlanningSchema::EMPLOI_ID, EmploiSchema::ID);
    }

    /**
     * Get the incidents created by this emploi.
     *
     * @return HasMany<Incident, $this>
     */
    public function incidents(): HasMany
    {
        /** @var HasMany<Incident, $this> */
        // @phpstan-ignore classConstant.notFound
        return $this->hasMany(Incident::class, IncidentSchema::EMPLOI_DECLARANT_ID, EmploiSchema::ID);
    }

    /**
     * Check if employee is currently active
     */
    public function isActive(): bool
    {
        // @phpstan-ignore property.notFound
        $estActif = $this->est_actif;
        // @phpstan-ignore property.notFound
        $dateFin = $this->date_fin;

        return $estActif && ($dateFin === null || $dateFin >= now()->toDateString());
    }

    /**
     * Check if employee is manager
     */
    public function isManager(): bool
    {
        // @phpstan-ignore property.notFound
        return in_array($this->niveau_acces, ['manager', 'directeur', 'admin']);
    }

    /**
     * Check if employee is admin
     */
    public function isAdmin(): bool
    {
        // @phpstan-ignore property.notFound
        return $this->niveau_acces === 'admin';
    }

    /**
     * Check if employee can manage other employees
     */
    public function canManage(): bool
    {
        // @phpstan-ignore property.notFound
        return in_array($this->niveau_acces, ['manager', 'directeur', 'admin']);
    }

    /**
     * Terminate employment
     */
    public function terminate(?string $reason = null, ?\Carbon\Carbon $endDate = null): bool
    {
        // @phpstan-ignore property.notFound
        if (!$this->est_actif) {
            return false;
        }

        $this->update([
            EmploiSchema::EST_ACTIF  => false,
            EmploiSchema::DATE_FIN   => $endDate ?? now()->toDateString(),
            EmploiSchema::RAISON_FIN => $reason,
        ]);

        return true;
    }

    /**
     * Get employment duration in days
     */
    public function getDurationInDays(): int
    {
        // @phpstan-ignore property.notFound
        $endDate = $this->date_fin ? \Carbon\Carbon::parse($this->date_fin) : now();

        // @phpstan-ignore property.notFound, return.type
        return \Carbon\Carbon::parse($this->date_debut)->diffInDays($endDate);
    }

    /**
     * Get display name for position
     */
    public function getPosteDisplayAttribute(): string
    {
        // @phpstan-ignore property.notFound
        return match ($this->poste) {
            'caissier'        => 'Caissier(ère)',
            'accueil'         => 'Accueil',
            'projectionniste' => 'Projectionniste',
            'maintenance'     => 'Maintenance',
            'menage'          => 'Ménage',
            'securite'        => 'Sécurité',
            'manager'         => 'Manager',
            'directeur'       => 'Directeur(trice)',
            // @phpstan-ignore property.notFound
            default => $this->poste,
        };
    }

    /**
     * Get display name for access level
     */
    public function getNiveauAccesDisplayAttribute(): string
    {
        // @phpstan-ignore property.notFound
        return match ($this->niveau_acces) {
            'employe'   => 'Employé',
            'manager'   => 'Manager',
            'directeur' => 'Directeur',
            'admin'     => 'Administrateur',
            // @phpstan-ignore property.notFound
            default => $this->niveau_acces,
        };
    }

    /**
     * Create a new factory instance for the model.
     *
     * @phpstan-ignore-next-line class.notFound
     */
    protected static function newFactory(): \Database\Factories\Employees\EmploiFactory
    {
        // @phpstan-ignore class.notFound
        return \Database\Factories\Employees\EmploiFactory::new();
    }

    protected function casts(): array
    {
        return [
            EmploiSchema::USER_PROFIL_KEY => 'integer',
            EmploiSchema::MANAGER_DB_ID   => 'integer',
            EmploiSchema::DATE_DEBUT      => 'date',
            EmploiSchema::DATE_FIN        => 'date',
            EmploiSchema::DATE_EMBAUCHE   => 'date',
            EmploiSchema::EST_ACTIF       => 'boolean',
            EmploiSchema::CREATED_AT      => 'timestamp',
            EmploiSchema::UPDATED_AT      => 'timestamp',
        ];
    }
}
