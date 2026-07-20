<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Cinema;

use App\Infrastructure\Casts\AsDevise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Cinema\ReservationSchema;

/**
 * @property string $uuid
 * @property string $seance_uuid
 * @property string $utilisateur_uuid
 * @property int $nombre_places
 * @property int $montant_total
 * @property string $devise
 * @property string $statut
 * @property \Illuminate\Support\Carbon $date_reservation
 * @property \Illuminate\Support\Carbon|null $date_expiration
 * @property array<string, mixed>|null $details_billets
 * @property array<string, mixed>|null $informations_paiement
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
final class Reservation extends Model
{
    use HasFactory;

    public $incrementing = true;

    protected $table = ReservationSchema::FULL_TABLE;

    protected $connection = ReservationSchema::CONNECTION;

    protected $primaryKey = ReservationSchema::PRIMARY_KEY;

    protected $keyType = 'int';

    protected $fillable = [
        ReservationSchema::ID,
        ReservationSchema::SEANCE_ID,
        ReservationSchema::UTILISATEUR_ID,
        ReservationSchema::NOMBRE_PLACES,
        ReservationSchema::MONTANT_TOTAL,
        ReservationSchema::DEVISE,
        ReservationSchema::STATUT,
        ReservationSchema::DATE_RESERVATION,
        ReservationSchema::DATE_EXPIRATION,
        ReservationSchema::DETAILS_BILLETS,
        ReservationSchema::INFORMATIONS_PAIEMENT,
    ];

    protected $casts = [
        ReservationSchema::ID                    => 'string',
        ReservationSchema::SEANCE_ID             => 'string',
        ReservationSchema::UTILISATEUR_ID        => 'string',
        ReservationSchema::NOMBRE_PLACES         => 'integer',
        ReservationSchema::MONTANT_TOTAL         => 'integer', // En centimes
        ReservationSchema::DEVISE                => AsDevise::class,
        ReservationSchema::STATUT                => 'string',
        ReservationSchema::DATE_RESERVATION      => 'datetime',
        ReservationSchema::DATE_EXPIRATION       => 'datetime',
        ReservationSchema::DETAILS_BILLETS       => 'array',
        ReservationSchema::INFORMATIONS_PAIEMENT => 'array',
        ReservationSchema::CREATED_AT            => 'datetime',
        ReservationSchema::UPDATED_AT            => 'datetime',
    ];

    protected $attributes = [
        ReservationSchema::STATUT => 'en_attente',
        ReservationSchema::DEVISE => 'EUR',
    ];

    /**
     * Relation vers la séance
     *
     * @return BelongsTo<Seance, $this>
     */
    public function seance(): BelongsTo
    {
        /** @var BelongsTo<Seance, $this> */
        return $this->belongsTo(
            Seance::class,
            ReservationSchema::SEANCE_ID,
            'uuid'
        );
    }

    /**
     * Relation vers l'utilisateur (si implémenté)
     *
     * @return BelongsTo<\App\Infrastructure\Database\Models\Auth\User, $this>
     */
    public function utilisateur(): BelongsTo
    {

        /** @var BelongsTo<\App\Infrastructure\Database\Models\Auth\User, $this> */
        return $this->belongsTo(
            \App\Infrastructure\Database\Models\Auth\User::class,
            ReservationSchema::UTILISATEUR_ID,
            'uuid'
        );
    }
}
