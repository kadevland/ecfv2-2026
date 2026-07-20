<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Cinema;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Cinema\SalleSchema;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;
use App\Infrastructure\Database\Schemas\Cinema\SeanceSchema;

/**
 * @property int $db_id
 * @property string $uuid
 * @property int $cinema_db_id
 * @property string $cinema_uuid
 * @property string $nom
 * @property int $capacite_totale
 * @property int $nombre_rangees
 * @property int $places_par_rangee
 * @property int $places_standard
 * @property int $places_pmr
 * @property array<string> $qualite_projection
 * @property array<string> $qualite_sonore
 * @property bool $climatisation
 * @property bool $accessibilite_pmr
 * @property array<string, mixed> $plan_salle
 * @property string $statut
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Cinema $cinema
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Seance> $seances
 */
final class Salle extends Model
{
    /** @use HasFactory<\Database\Factories\Cinema\SalleFactory> */
    use HasFactory;

    public const RELATION_CINEMA = 'cinema';

    public const RELATION_SEANCES = 'seances';

    protected $connection = SalleSchema::CONNECTION;

    protected $table = SalleSchema::FULL_TABLE;

    protected $primaryKey = SalleSchema::PRIMARY_KEY; // db_id auto-increment

    protected $fillable = [
        SalleSchema::ID,
        SalleSchema::CINEMA_KEY,
        SalleSchema::CINEMA_ID,
        SalleSchema::NOM,
        SalleSchema::CAPACITE_TOTALE,
        SalleSchema::NOMBRE_RANGEES,
        SalleSchema::PLACES_PAR_RANGEE,
        SalleSchema::PLACES_STANDARD,
        SalleSchema::PLACES_PMR,
        SalleSchema::QUALITE_PROJECTION,
        SalleSchema::QUALITE_SONORE,
        SalleSchema::CLIMATISATION,
        SalleSchema::ACCESSIBILITE_PMR,
        SalleSchema::PLAN_SALLE,
        SalleSchema::STATUT,
    ];

    /**
     * Get the cinema that owns this salle.
     *
     * @return BelongsTo<Cinema, $this>
     */
    public function cinema(): BelongsTo
    {
        /** @var BelongsTo<Cinema, $this> */
        return $this->belongsTo(Cinema::class, SalleSchema::CINEMA_KEY, CinemaSchema::PRIMARY_KEY);
    }

    /**
     * Get the seances for this salle.
     *
     * @return HasMany<Seance, $this>
     */
    public function seances(): HasMany
    {
        /** @var HasMany<Seance, $this> */
        return $this->hasMany(Seance::class, SeanceSchema::SALLE_KEY, SalleSchema::PRIMARY_KEY);
    }

    /**
     * Get full name with cinema
     */
    public function getFullNameAttribute(): string
    {
        return $this->cinema->nom . ' - ' . $this->nom;
    }

    /**
     * Check if salle is accessible for wheelchair
     */
    public function isWheelchairAccessible(): bool
    {
        return ($this->accessibilite_pmr ?? false) === true;
    }

    /**
     * Get available seats for a specific seance
     */
    // public function getAvailableSeatsForSeance(string $seanceId): array
    // {
    //
    //     // This would typically involve checking reservations for the seance
    //     return $this->configuration_sieges;
    // }

    /**
     * Get seat configuration by row
     */
    // public function getSeatsByRow(): array
    // {
    //     $seatsByRow = [];
    //     foreach ($this->configuration_sieges as $seat) {
    //         $row                = $seat['row'] ?? 'A';
    //         $seatsByRow[$row][] = $seat;
    //     }

    //     return $seatsByRow;
    // }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \Database\Factories\Cinema\SalleFactory
    {
        return \Database\Factories\Cinema\SalleFactory::new();
    }

    protected function casts(): array
    {
        return [
            SalleSchema::CAPACITE_TOTALE    => 'integer',
            SalleSchema::NOMBRE_RANGEES     => 'integer',
            SalleSchema::PLACES_PAR_RANGEE  => 'integer',
            SalleSchema::PLACES_STANDARD    => 'integer',
            SalleSchema::PLACES_PMR         => 'integer',
            SalleSchema::QUALITE_PROJECTION => 'array',
            SalleSchema::QUALITE_SONORE     => 'array',
            SalleSchema::CLIMATISATION      => 'boolean',
            SalleSchema::ACCESSIBILITE_PMR  => 'boolean',
            SalleSchema::PLAN_SALLE         => 'array',
            SalleSchema::CREATED_AT         => 'timestamp',
            SalleSchema::UPDATED_AT         => 'timestamp',
        ];
    }
}
