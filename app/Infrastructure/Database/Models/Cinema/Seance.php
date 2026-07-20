<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Cinema;

use App\Domain\Enums\StatutSeance;
use Illuminate\Database\Eloquent\Builder;
use App\Infrastructure\Casts\AsDevise;
use App\Infrastructure\Casts\AsTauxTva;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Infrastructure\Casts\AsTarification;
use App\Domain\Cinema\Enums\QualiteProjection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Cinema\FilmSchema;
use App\Infrastructure\Database\Schemas\Cinema\SalleSchema;
use App\Infrastructure\Database\Schemas\Cinema\SeanceSchema;
use App\Infrastructure\Database\Schemas\Reservations\ReservationSchema;

/**
 * @property string $id
 * @property string $film_uuid
 * @property string $salle_uuid
 * @property \Illuminate\Support\Carbon $date_heure_debut
 * @property \Illuminate\Support\Carbon $date_heure_fin
 * @property string $version
 * @property \App\Domain\Cinema\ValueObjects\Tarification $tarification
 * @property \App\Domain\Shared\ValueObjects\TauxTva $taux_tva
 * @property \App\Domain\Shared\ValueObjects\Devise $devise
 * @property bool $placement_libre
 * @property string $statut
 * @property array<string, mixed>|null $options_supplementaires
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Film $film
 * @property-read Salle $salle
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Infrastructure\Database\Models\Reservations\Reservation> $reservations
 */
final class Seance extends Model
{
    use HasFactory;

    protected $connection = SeanceSchema::CONNECTION;

    protected $table = SeanceSchema::FULL_TABLE;

    protected $primaryKey = SeanceSchema::PRIMARY_KEY; // db_id auto-increment

    protected $fillable = [
        SeanceSchema::ID,
        SeanceSchema::FILM_ID,
        SeanceSchema::SALLE_ID,
        SeanceSchema::DATE_HEURE_DEBUT,
        SeanceSchema::DATE_HEURE_FIN,
        SeanceSchema::VERSION,
        SeanceSchema::TARIFICATION,
        SeanceSchema::TAUX_TVA,
        SeanceSchema::DEVISE,
        SeanceSchema::PLACEMENT_LIBRE,
        SeanceSchema::STATUT,
        SeanceSchema::DUREE_ADDITIONNELLE,
        SeanceSchema::QUALITE_PROJECTION,
        SeanceSchema::QUALITE_SONORE,
    ];

    /**
     * Get the film for this seance.
     *
     * @return BelongsTo<Film, $this>
     */
    public function film () : BelongsTo
    {
        /** @var BelongsTo<Film, $this> */
        return $this->belongsTo(Film::class, SeanceSchema::FILM_KEY, FilmSchema::PRIMARY_KEY);
    }

    /**
     * Get the salle for this seance.
     *
     * @return BelongsTo<Salle, $this>
     */
    public function salle () : BelongsTo
    {
        /** @var BelongsTo<Salle, $this> */
        return $this->belongsTo(Salle::class, SeanceSchema::SALLE_KEY, SalleSchema::PRIMARY_KEY);
    }

    /**
     * Get the reservations for this seance.
     *
     * @return HasMany<\App\Infrastructure\Database\Models\Reservations\Reservation, $this>
     */
    public function reservations () : HasMany
    {
        /** @var HasMany<\App\Infrastructure\Database\Models\Reservations\Reservation, $this> */
        return $this->hasMany(\App\Infrastructure\Database\Models\Reservations\Reservation::class, ReservationSchema::SEANCE_KEY, SeanceSchema::PRIMARY_KEY);
    }

    /**
     * Get tarification value object
     */
    public function getTarification () : \App\Domain\Cinema\ValueObjects\Tarification
    {
        return $this->tarification;
    }

    /**
     * Check if seance is in the past
     */
    public function isPast () : bool
    {
        return $this->date_heure_debut < now();
    }

    /**
     * Check if seance is currently playing
     */
    public function isPlaying () : bool
    {
        $now = now();

        return $this->date_heure_debut <= $now && $this->date_heure_fin > $now;
    }

    /**
     * Check if seance is upcoming
     */
    public function isUpcoming () : bool
    {
        return $this->date_heure_debut > now();
    }

    /**
     * Get available seats count
     */
    public function getAvailableSeatsCount () : int
    {
        $totalSeats    = $this->salle->capacite_totale;
        $reservedSeats = $this->reservations()
            ->where(ReservationSchema::STATUT, '!=', 'annulee')
            ->sum(ReservationSchema::NOMBRE_PLACES);

        return $totalSeats - $reservedSeats;
    }

    /**
     * Check if seance is sold out
     */
    public function isSoldOut () : bool
    {
        return $this->getAvailableSeatsCount() <= 0;
    }

    /**
     * Get formatted time (ex: "20:30")
     */
    public function getFormattedTimeAttribute () : string
    {
        return $this->date_heure_debut->format('H:i');
    }

    #[Scope]

    public function wherePlaying (Builder $query) : void
    {
        $now = now();
        $query->where(SeanceSchema::DATE_HEURE_DEBUT, '>=', $now);

    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory () : mixed
    {
        return \Database\Factories\Cinema\SeanceFactory::new(); // @phpstan-ignore-line
    }

    protected function casts () : array
    {
        return [
            SeanceSchema::DATE_HEURE_DEBUT    => 'datetime',
            SeanceSchema::DATE_HEURE_FIN      => 'datetime',
            SeanceSchema::TARIFICATION        => AsTarification::class,
            SeanceSchema::TAUX_TVA            => AsTauxTva::class,
            SeanceSchema::DEVISE              => AsDevise::class,
            SeanceSchema::PLACEMENT_LIBRE     => 'boolean',
            SeanceSchema::DUREE_ADDITIONNELLE => 'integer',
            SeanceSchema::QUALITE_PROJECTION  => QualiteProjection::class,
            SeanceSchema::QUALITE_SONORE      => QualiteSonore::class,
            SeanceSchema::STATUT              => StatutSeance::class,
            SeanceSchema::CREATED_AT          => 'timestamp',
            SeanceSchema::UPDATED_AT          => 'timestamp',
        ];
    }
}
