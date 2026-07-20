<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Reservations;

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Database\Models\Auth\User;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Infrastructure\Database\Models\Cinema\Seance;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Schemas\Cinema\SeanceSchema;
use App\Infrastructure\Database\Schemas\Reservations\BilletSchema;
use App\Infrastructure\Database\Schemas\Reservations\PaiementSchema;
use App\Infrastructure\Database\Schemas\Reservations\ReservationSchema;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
/**
 * @property string $id
 * @property string $numero_reservation
 * @property int $user_db_id
 * @property string $user_uuid
 * @property string $seance_id
 * @property int $nombre_places
 * @property array $places_details
 * @property \Money\Money $montant_total
 * @property string $montant_total_devise
 * @property \Money\Money $montant_ht
 * @property string $montant_ht_devise
 * @property \App\Domain\Shared\ValueObjects\TauxTva $taux_tva
 * @property string $statut
 * @property \Illuminate\Support\Carbon|null $date_expiration
 * @property string|null $commentaires
 * @property string|null $qr_code
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read User $user
 * @property-read Seance $seance
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Billet> $billets
 * @property-read Paiement|null $paiement
 */
final class Reservation extends Model
{
    use HasFactory;

    protected $connection = ReservationSchema::CONNECTION;

    protected $table = ReservationSchema::FULL_TABLE;

    protected $primaryKey = ReservationSchema::PRIMARY_KEY; // db_id auto-increment

    protected $fillable = [
        ReservationSchema::ID,
        ReservationSchema::NUMERO_RESERVATION,
        ReservationSchema::USER_KEY,
        ReservationSchema::USER_ID,
        ReservationSchema::SEANCE_KEY,
        ReservationSchema::SEANCE_ID,
        ReservationSchema::NOMBRE_PLACES,
        ReservationSchema::DETAILS_PLACES,
        ReservationSchema::STATUT,
        ReservationSchema::PRIX_UNITAIRE_HT_CENTIMES,
        ReservationSchema::PRIX_TOTAL_HT_CENTIMES,
        ReservationSchema::PRIX_TOTAL_TTC_CENTIMES,
        ReservationSchema::DEVISE,
        ReservationSchema::TAUX_TVA_BASIS_POINTS,
        ReservationSchema::DATE_RESERVATION,
        ReservationSchema::DATE_EXPIRATION,
        ReservationSchema::DATE_CONFIRMATION,
        ReservationSchema::DATE_UTILISATION,
        ReservationSchema::EMAIL_CONFIRMATION,
        ReservationSchema::TELEPHONE_CONTACT,
        ReservationSchema::CODE_CONFIRMATION,
        ReservationSchema::TOKEN_SECURITE,
        ReservationSchema::CANAL_RESERVATION,
        ReservationSchema::CODE_PROMOTION,
        ReservationSchema::REMISE_CENTIMES,
        ReservationSchema::IP_RESERVATION,
        ReservationSchema::USER_AGENT,
        ReservationSchema::METADONNEES_RESERVATION,
        ReservationSchema::NOTES_CLIENT,
        ReservationSchema::NOTES_INTERNES,
    ];

    /**
     * Get the user that owns this reservation.
     *
     * @return BelongsTo<User, $this>
     */
    public function user () : BelongsTo
    {
        /** @var BelongsTo<User, $this> */
        return $this->belongsTo(User::class, ReservationSchema::USER_KEY, UserSchema::PRIMARY_KEY);
    }

    /**
     * Get the seance for this reservation.
     *
     * @return BelongsTo<Seance, $this>
     */
    public function seance () : BelongsTo
    {
        /** @var BelongsTo<Seance, $this> */
        return $this->belongsTo(Seance::class, ReservationSchema::SEANCE_KEY, SeanceSchema::PRIMARY_KEY);
    }

    /**
     * Get the billets for this reservation.
     *
     * @return HasMany<Billet, $this>
     */
    public function billets () : HasMany
    {
        /** @var HasMany<Billet, $this> */
        return $this->hasMany(Billet::class, BilletSchema::RESERVATION_KEY, ReservationSchema::PRIMARY_KEY);
    }

    /**
     * Get the paiement for this reservation.
     *
     * @return HasOne<Paiement, $this>
     */
    public function paiement () : HasOne
    {
        /** @var HasOne<Paiement, $this> */
        return $this->hasOne(Paiement::class, PaiementSchema::RESERVATION_ID, ReservationSchema::ID);
    }

    /**
     * Check if reservation is confirmed
     */
    public function isConfirmed () : bool
    {
        return $this->statut === 'confirmee';
    }

    /**
     * Check if reservation is cancelled
     */
    public function isCancelled () : bool
    {
        return $this->statut === 'annulee';
    }

    /**
     * Check if reservation is expired
     */
    public function isExpired () : bool
    {
        return $this->date_expiration && $this->date_expiration < now();
    }

    /**
     * Check if reservation is paid
     */
    public function isPaid () : bool
    {
        return $this->paiement && $this->paiement->statut === 'valide';
    }

    /**
     * Get seat numbers
     */
    public function getSeatNumbers () : array
    {
        $seats = [];
        foreach ($this->places_details[ReservationSchema::PLACES] ?? [] as $place) {
            $seats[] = $place[ReservationSchema::RANGEE] . $place[ReservationSchema::NUMERO_PLACE];
        }

        return $seats;
    }

    /**
     * Generate QR code for mobile app (US14)
     */
    public function generateQrCode () : string
    {
        if (!$this->qr_code) {
            $qrData = [
                'reservation_id' => $this->id,
                'numero'         => $this->numero_reservation,
                'seance_id'      => $this->seance_id,
                'user_uuid'      => $this->user_uuid,
                'places'         => $this->nombre_places,
                'timestamp'      => now()->timestamp,
            ];

            $this->qr_code = base64_encode(json_encode($qrData));
            $this->save();
        }

        return $this->qr_code;
    }

    /**
     * Cancel reservation
     */
    public function cancel (?string $reason = null) : bool
    {
        if ($this->isCancelled()) {
            return false;
        }

        $this->update([
            ReservationSchema::STATUT       => 'annulee',
            ReservationSchema::COMMENTAIRES => $reason,
        ]);

        return true;
    }

    #[Scope]
    public function whereForPlayingSceance (Builder $query) : void
    {
        $query->whereHas('seance', function (Builder $q) {
            $q->where('date_heure_debut', '>=', now());
        });

    }

    /**
     * Create a new factory instance for the model.
     */
    /** @phpstan-ignore-next-line class.notFound */
    protected static function newFactory () : \Database\Factories\Reservations\ReservationFactory
    {
        /** @phpstan-ignore-next-line class.notFound */
        return \Database\Factories\Reservations\ReservationFactory::new();
    }

    protected function casts () : array
    {
        return [
            ReservationSchema::NOMBRE_PLACES             => 'integer',
            ReservationSchema::DETAILS_PLACES            => 'array',
            ReservationSchema::PRIX_UNITAIRE_HT_CENTIMES => 'integer',
            ReservationSchema::PRIX_TOTAL_HT_CENTIMES    => 'integer',
            ReservationSchema::PRIX_TOTAL_TTC_CENTIMES   => 'integer',
            ReservationSchema::TAUX_TVA_BASIS_POINTS     => 'integer',
            ReservationSchema::REMISE_CENTIMES           => 'integer',
            ReservationSchema::DATE_RESERVATION          => 'datetime',
            ReservationSchema::DATE_EXPIRATION           => 'datetime',
            ReservationSchema::DATE_CONFIRMATION         => 'datetime',
            ReservationSchema::DATE_UTILISATION          => 'datetime',
            ReservationSchema::METADONNEES_RESERVATION   => 'array',
            ReservationSchema::CREATED_AT                => 'datetime',
            ReservationSchema::UPDATED_AT                => 'datetime',
        ];
    }
}
