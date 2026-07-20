<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Employees;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Employees\EmploiSchema;
use App\Infrastructure\Database\Schemas\Employees\PlanningSchema;

/**
 * @property int $id
 * @property string $emploi_id
 * @property \Illuminate\Support\Carbon $date_travail
 * @property string $heure_debut
 * @property string $heure_fin
 * @property int|null $pause_duree
 * @property string $type_service
 * @property string $statut
 * @property string|null $remplacant_id
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Emploi $emploi
 * @property-read Emploi|null $remplacant
 */
final class Planning extends Model
{
    use HasFactory;

    protected $connection = PlanningSchema::CONNECTION;

    protected $table = PlanningSchema::FULL_TABLE;

    protected $primaryKey = PlanningSchema::PRIMARY_KEY; // db_id auto-increment

    protected $fillable = [
        PlanningSchema::EMPLOI_ID,
        PlanningSchema::DATE_TRAVAIL,
        PlanningSchema::HEURE_DEBUT,
        PlanningSchema::HEURE_FIN,
        PlanningSchema::PAUSE_DUREE,
        PlanningSchema::TYPE_SERVICE,
        PlanningSchema::STATUT,
        PlanningSchema::REMPLACANT_ID,
        PlanningSchema::NOTES,
    ];

    /**
     * Get the emploi that owns this planning.
     *
     * @return BelongsTo<Emploi, $this>
     */
    public function emploi(): BelongsTo
    {
        /** @var BelongsTo<Emploi, $this> */
        return $this->belongsTo(Emploi::class, PlanningSchema::EMPLOI_ID, EmploiSchema::ID);
    }

    /**
     * Get the replacement emploi for this planning.
     *
     * @return BelongsTo<Emploi, $this>
     */
    public function remplacant(): BelongsTo
    {
        /** @var BelongsTo<Emploi, $this> */
        return $this->belongsTo(Emploi::class, PlanningSchema::REMPLACANT_ID, EmploiSchema::ID);
    }

    /**
     * Check if shift is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->statut === 'confirme';
    }

    /**
     * Check if shift is pending
     */
    public function isPending(): bool
    {
        return $this->statut === 'en_attente';
    }

    /**
     * Check if shift is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->statut === 'annule';
    }

    /**
     * Check if shift is completed
     */
    public function isCompleted(): bool
    {
        return $this->statut === 'termine';
    }

    /**
     * Check if shift is in the past
     */
    public function isPast(): bool
    {
        return $this->date_travail < now()->toDateString();
    }

    /**
     * Check if shift is today
     */
    public function isToday(): bool
    {
        return $this->date_travail->isToday();
    }

    /**
     * Check if shift is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->date_travail > now()->toDateString();
    }

    /**
     * Get shift duration in minutes
     */
    public function getDurationInMinutes(): int
    {
        $debut = \Carbon\Carbon::createFromTimeString($this->heure_debut);
        $fin   = \Carbon\Carbon::createFromTimeString($this->heure_fin);

        $duration = $debut->diffInMinutes($fin);

        // Subtract pause duration
        if ($this->pause_duree) {
            $duration -= $this->pause_duree;
        }

        return (int) $duration;
    }

    /**
     * Get formatted shift time (ex: "08:00 - 16:00")
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->heure_debut . ' - ' . $this->heure_fin;
    }

    /**
     * Get shift duration as formatted string (ex: "7h30")
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes          = $this->getDurationInMinutes();
        $hours            = intval($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes > 0) {
            return "{$hours}h{$remainingMinutes}";
        } else {
            return "{$hours}h";
        }
    }

    /**
     * Confirm the shift
     */
    public function confirm(): bool
    {
        if ($this->statut !== 'en_attente') {
            return false;
        }

        $this->update([PlanningSchema::STATUT => 'confirme']);

        return true;
    }

    /**
     * Cancel the shift
     */
    public function cancel(?string $reason = null): bool
    {
        if ($this->isCompleted()) {
            return false;
        }

        $this->update([
            PlanningSchema::STATUT => 'annule',
            PlanningSchema::NOTES  => $reason,
        ]);

        return true;
    }

    /**
     * Mark shift as completed
     */
    public function complete(): bool
    {
        if (!$this->isConfirmed() || !$this->isPast()) {
            return false;
        }

        $this->update([PlanningSchema::STATUT => 'termine']);

        return true;
    }

    /**
     * Get service type display name
     */
    public function getTypeServiceDisplayAttribute(): string
    {
        return match ($this->type_service) {
            'matin'            => 'Service matin',
            'apres_midi'       => 'Service après-midi',
            'soiree'           => 'Service soirée',
            'nuit'             => 'Service nuit',
            'journee_continue' => 'Journée continue',
            'coupure'          => 'Service avec coupure',
            default            => $this->type_service,
        };
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): mixed
    {
        return \Database\Factories\Employees\PlanningFactory::new(); // @phpstan-ignore-line
    }

    protected function casts(): array
    {
        return [
            PlanningSchema::DATE_TRAVAIL => 'date',
            PlanningSchema::PAUSE_DUREE  => 'integer',
            PlanningSchema::CREATED_AT   => 'timestamp',
            PlanningSchema::UPDATED_AT   => 'timestamp',
        ];
    }
}
