<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Employees;

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Database\Models\Auth\User;
use App\Infrastructure\Database\Models\Cinema\Salle;
use App\Infrastructure\Database\Models\Cinema\Cinema;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Schemas\Cinema\SalleSchema;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;
use App\Infrastructure\Database\Schemas\Employees\EmploiSchema;

/**
 * @property int $id
 * @property string $employee_id
 * @property string $cinema_id
 * @property string|null $salle_id
 * @property string $type_incident
 * @property string $severite
 * @property string $titre
 * @property string $description
 * @property string $statut
 * @property \Illuminate\Support\Carbon|null $date_resolution
 * @property string|null $responsable_resolution
 * @property array<string, mixed>|null $pieces_jointes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Emploi $emploiDeclarant
 * @property-read Cinema $cinema
 * @property-read Salle|null $salle
 * @property-read User|null $responsableResolution
 */
final class Incident extends Model
{
    use HasFactory;

    protected $connection = 'pgsql';

    protected $table = 'employees.incidents';

    protected $primaryKey = 'db_id';

    protected $fillable = [
        'uuid',
        'numero_incident',
        'contrat_rapporteur_id',
        'cinema_db_id',
        'cinema_uuid',
        'salle_db_id',
        'seance_db_id',
        'type_incident',
        'categorie',
        'niveau_gravite',
        'niveau_priorite',
        'date_incident',
        'date_rapport',
        'date_prise_en_compte',
        'date_resolution',
        'titre',
        'description',
        'actions_immediates',
        'consequences',
        'personnes_impliquees',
        'temoins',
        'degats_materiels',
        'cout_degats_centimes',
        'devise',
        'assurance_impliquee',
        'numero_sinistre',
        'statut',
        'assigne_a_contrat_id',
        'plan_action',
        'resolution_finale',
        'causes_racines',
        'mesures_preventives',
        'formation_requise',
        'recommandations',
        'declaration_obligatoire',
        'declaration_effectuee',
        'date_declaration',
        'organisme_declare',
        'photos_urls',
        'documents_urls',
        'videos_urls',
        'device_id',
        'app_version',
        'ip_saisie',
        'metadonnees_technique',
        'historique_workflow',
        'notifications_envoyees',
        'notes_complementaires',
    ];

    /**
     * Get the emploi (employee) that reported this incident.
     *
     * @return BelongsTo<Emploi, $this>
     */
    public function emploiDeclarant(): BelongsTo
    {
        /** @var BelongsTo<Emploi, $this> */
        return $this->belongsTo(Emploi::class, 'employee_id', EmploiSchema::ID);
    }

    /**
     * Get the cinema where this incident occurred.
     *
     * @return BelongsTo<Cinema, $this>
     */
    public function cinema(): BelongsTo
    {
        /** @var BelongsTo<Cinema, $this> */
        return $this->belongsTo(Cinema::class, 'cinema_id', CinemaSchema::ID);
    }

    /**
     * Get the salle where this incident occurred (optional).
     *
     * @return BelongsTo<Salle, $this>
     */
    public function salle(): BelongsTo
    {
        /** @var BelongsTo<Salle, $this> */
        return $this->belongsTo(Salle::class, 'salle_id', SalleSchema::ID);
    }

    /**
     * Get the user responsible for resolution.
     *
     * @return BelongsTo<User, $this>
     */
    public function responsableResolution(): BelongsTo
    {
        /** @var BelongsTo<User, $this> */
        return $this->belongsTo(User::class, 'responsable_resolution', UserSchema::PRIMARY_KEY);
    }

    /**
     * Check if incident is open
     */
    public function isOpen(): bool
    {
        return $this->statut === 'ouvert';
    }

    /**
     * Check if incident is in progress
     */
    public function isInProgress(): bool
    {
        return $this->statut === 'en_cours';
    }

    /**
     * Check if incident is resolved
     */
    public function isResolved(): bool
    {
        return $this->statut === 'resolu';
    }

    /**
     * Check if incident is closed
     */
    public function isClosed(): bool
    {
        return $this->statut === 'ferme';
    }

    /**
     * Check if incident is high priority
     */
    public function isHighPriority(): bool
    {
        return $this->severite === 'critique' || $this->severite === 'majeure';
    }

    /**
     * Check if incident is critical
     */
    public function isCritical(): bool
    {
        return $this->severite === 'critique';
    }

    /**
     * Assign incident to a user
     */
    public function assignTo(int $userId): bool
    {
        if ($this->isClosed()) {
            return false;
        }

        $this->update([
            'responsable_resolution' => $userId,
            'statut'                 => 'en_cours',
        ]);

        return true;
    }

    /**
     * Resolve incident
     */
    public function resolve(?int $resolvedBy = null): bool
    {
        if ($this->isClosed()) {
            return false;
        }

        $this->update([
            'statut'                 => 'resolu',
            'date_resolution'        => now(),
            'responsable_resolution' => $resolvedBy ?? $this->responsable_resolution,
        ]);

        return true;
    }

    /**
     * Close incident
     */
    public function close(): bool
    {
        if (!$this->isResolved()) {
            return false;
        }

        $this->update(['statut' => 'ferme']);

        return true;
    }

    /**
     * Reopen incident
     */
    public function reopen(): bool
    {
        if (!$this->isClosed() && !$this->isResolved()) {
            return false;
        }

        $this->update([
            'statut'          => 'ouvert',
            'date_resolution' => null,
        ]);

        return true;
    }

    /**
     * Get incident age in hours
     */
    public function getAgeInHours(): int
    {
        return (int) $this->created_at->diffInHours(now());
    }

    /**
     * Get resolution time in hours
     */
    public function getResolutionTimeInHours(): ?int
    {
        if (!$this->date_resolution) {
            return null;
        }

        return (int) $this->created_at->diffInHours($this->date_resolution);
    }

    /**
     * Get incident type display name
     */
    public function getTypeIncidentDisplayAttribute(): string
    {
        return match ($this->type_incident) {
            'technique'     => 'Incident technique',
            'maintenance'   => 'Maintenance',
            'securite'      => 'Sécurité',
            'nettoyage'     => 'Nettoyage',
            'client'        => 'Incident client',
            'equipement'    => 'Équipement',
            'projection'    => 'Projection',
            'son'           => 'Son/Audio',
            'eclairage'     => 'Éclairage',
            'climatisation' => 'Climatisation',
            'autre'         => 'Autre',
            default         => $this->type_incident,
        };
    }

    /**
     * Get severity display name with color
     */
    public function getSeveriteDisplayAttribute(): string
    {
        return match ($this->severite) {
            'critique' => 'Critique',
            'majeure'  => 'Majeure',
            'normale'  => 'Normale',
            'mineure'  => 'Mineure',
            default    => $this->severite,
        };
    }

    /**
     * Get status display name
     */
    public function getStatutDisplayAttribute(): string
    {
        return match ($this->statut) {
            'ouvert'   => 'Ouvert',
            'en_cours' => 'En cours',
            'resolu'   => 'Résolu',
            'ferme'    => 'Fermé',
            default    => $this->statut,
        };
    }

    /**
     * Add attachment to incident
     */
    public function addAttachment(string $filename, string $path, ?string $type = null): void
    {
        $attachments = $this->pieces_jointes ?? [];

        $attachments[] = [
            'filename'    => $filename,
            'path'        => $path,
            'type'        => $type,
            'uploaded_at' => now()->toISOString(),
        ];

        $this->update(['pieces_jointes' => $attachments]);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): mixed
    {
        return \Database\Factories\Employees\IncidentFactory::new(); // @phpstan-ignore-line
    }

    protected function casts(): array
    {
        return [
            'date_resolution' => 'datetime',
            'pieces_jointes'  => 'array',
            'created_at'      => 'timestamp',
            'updated_at'      => 'timestamp',
        ];
    }
}
