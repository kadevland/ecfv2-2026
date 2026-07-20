<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Reservations;

use App\Infrastructure\Casts\AsMoney;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Database\Models\Auth\User;
use App\Infrastructure\Database\Models\Cinema\Seance;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Infrastructure\Database\Schemas\Cinema\SeanceSchema;
use App\Infrastructure\Database\Schemas\Reservations\BilletSchema;
use App\Infrastructure\Database\Schemas\Reservations\ReservationSchema;

/**
 * @property string $id
 * @property string $numero_billet
 * @property string $reservation_id
 * @property string $seance_id
 * @property bool $placement_libre
 * @property string|null $rangee
 * @property int|null $numero_siege
 * @property string $type_tarif
 * @property \Money\Money $prix_unitaire
 * @property string $prix_unitaire_devise
 * @property string $statut_billet
 * @property string|null $qr_code_individuel
 * @property \Illuminate\Support\Carbon|null $date_utilisation
 * @property int|null $controle_par
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Reservation $reservation
 * @property-read Seance $seance
 * @property-read User|null $controlePar
 */
final class Billet extends Model
{
    use HasFactory;

    protected $connection = BilletSchema::CONNECTION;

    protected $table = BilletSchema::FULL_TABLE;

    protected $primaryKey = BilletSchema::PRIMARY_KEY; // db_id auto-increment

    protected $fillable = [
        BilletSchema::ID,
        BilletSchema::NUMERO_BILLET,
        BilletSchema::RESERVATION_ID,
        BilletSchema::SEANCE_ID,
        BilletSchema::PLACEMENT_LIBRE,
        BilletSchema::RANGEE,
        BilletSchema::NUMERO_SIEGE,
        BilletSchema::TYPE_TARIF,
        BilletSchema::PRIX_UNITAIRE,
        BilletSchema::PRIX_UNITAIRE_DEVISE,
        BilletSchema::STATUT_BILLET,
        BilletSchema::QR_CODE_INDIVIDUEL,
        BilletSchema::DATE_UTILISATION,
        BilletSchema::CONTROLE_PAR,
    ];

    /**
     * Get the reservation that owns this billet.
     *
     * @return BelongsTo<Reservation, $this>
     */
    public function reservation(): BelongsTo
    {
        /** @var BelongsTo<Reservation, $this> */
        return $this->belongsTo(Reservation::class, BilletSchema::RESERVATION_ID, ReservationSchema::ID);
    }

    /**
     * Get the seance for this billet.
     *
     * @return BelongsTo<Seance, $this>
     */
    public function seance(): BelongsTo
    {
        /** @var BelongsTo<Seance, $this> */
        return $this->belongsTo(Seance::class, BilletSchema::SEANCE_ID, SeanceSchema::ID);
    }

    /**
     * Get the employee who checked this ticket.
     *
     * @return BelongsTo<User, $this>
     */
    public function controlePar(): BelongsTo
    {
        /** @var BelongsTo<User, $this> */
        return $this->belongsTo(User::class, BilletSchema::CONTROLE_PAR, UserSchema::PRIMARY_KEY);
    }

    /**
     * Get seat designation (ex: "A12")
     */
    public function getSeatDesignationAttribute(): ?string
    {
        if ($this->placement_libre || !$this->rangee || !$this->numero_siege) {
            return null;
        }

        return $this->rangee . $this->numero_siege;
    }

    /**
     * Check if ticket is used
     */
    public function isUsed(): bool
    {
        return $this->statut_billet === 'utilise' && $this->date_utilisation !== null;
    }

    /**
     * Check if ticket is valid
     */
    public function isValid(): bool
    {
        return $this->statut_billet === 'valide' && !$this->isUsed();
    }

    /**
     * Check if ticket is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->statut_billet === 'annule';
    }

    /**
     * Generate individual QR code for this ticket (US14)
     */
    public function generateQrCode(): string
    {
        if (!$this->qr_code_individuel) {
            $qrData = [
                'billet_id'      => $this->id,
                'numero'         => $this->numero_billet,
                'reservation_id' => $this->reservation_id,
                'seance_id'      => $this->seance_id,
                'seat'           => $this->seat_designation,
                'timestamp'      => now()->timestamp,
            ];

            $this->qr_code_individuel = base64_encode(json_encode($qrData));
            $this->save();
        }

        return $this->qr_code_individuel;
    }

    /**
     * Mark ticket as used (US15 - Desktop app incident reporting)
     */
    public function markAsUsed(?int $controlledBy = null): bool
    {
        if ($this->isUsed() || !$this->isValid()) {
            return false;
        }

        $this->update([
            BilletSchema::STATUT_BILLET    => 'utilise',
            BilletSchema::DATE_UTILISATION => now(),
            BilletSchema::CONTROLE_PAR     => $controlledBy,
        ]);

        return true;
    }

    /**
     * Cancel ticket
     */
    public function cancel(): bool
    {
        if ($this->isUsed() || $this->isCancelled()) {
            return false;
        }

        $this->update([
            BilletSchema::STATUT_BILLET => 'annule',
        ]);

        return true;
    }

    /**
     * Create a new factory instance for the model.
     */
    /** @phpstan-ignore-next-line class.notFound */
    protected static function newFactory(): \Database\Factories\Reservations\BilletFactory
    {
        /** @phpstan-ignore-next-line class.notFound */
        return \Database\Factories\Reservations\BilletFactory::new();
    }

    protected function casts(): array
    {
        return [
            BilletSchema::PLACEMENT_LIBRE  => 'boolean',
            BilletSchema::NUMERO_SIEGE     => 'integer',
            BilletSchema::PRIX_UNITAIRE    => AsMoney::class,
            BilletSchema::DATE_UTILISATION => 'datetime',
            BilletSchema::CONTROLE_PAR     => 'integer',
            BilletSchema::CREATED_AT       => 'timestamp',
            BilletSchema::UPDATED_AT       => 'timestamp',
        ];
    }
}
