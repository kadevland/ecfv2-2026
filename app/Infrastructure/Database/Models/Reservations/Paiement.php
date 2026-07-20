<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Reservations;

use App\Infrastructure\Casts\AsMoney;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Reservations\PaiementSchema;
use App\Infrastructure\Database\Schemas\Reservations\ReservationSchema;

/**
 * @property string $id
 * @property string $reservation_id
 * @property string $numero_transaction
 * @property string $methode_paiement
 * @property string $statut_paiement
 * @property \Money\Money $montant_demande
 * @property string $montant_demande_devise
 * @property \Money\Money|null $montant_paye
 * @property string|null $montant_paye_devise
 * @property \Money\Money|null $frais_transaction
 * @property string|null $frais_transaction_devise
 * @property string|null $reference_externe
 * @property string|null $reference_banque
 * @property \Illuminate\Support\Carbon $date_demande
 * @property \Illuminate\Support\Carbon|null $date_autorisation
 * @property \Illuminate\Support\Carbon|null $date_capture
 * @property \Illuminate\Support\Carbon|null $date_expiration
 * @property array|null $donnees_paiement
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Reservation $reservation
 */
final class Paiement extends Model
{
    use HasFactory;

    protected $connection = PaiementSchema::CONNECTION;

    protected $table = PaiementSchema::FULL_TABLE;

    protected $primaryKey = PaiementSchema::PRIMARY_KEY; // db_id auto-increment

    protected $fillable = [
        PaiementSchema::ID,
        PaiementSchema::RESERVATION_ID,
        PaiementSchema::NUMERO_TRANSACTION,
        PaiementSchema::METHODE_PAIEMENT,
        PaiementSchema::STATUT_PAIEMENT,
        PaiementSchema::MONTANT_DEMANDE,
        PaiementSchema::MONTANT_DEMANDE_DEVISE,
        PaiementSchema::MONTANT_PAYE,
        PaiementSchema::MONTANT_PAYE_DEVISE,
        PaiementSchema::FRAIS_TRANSACTION,
        PaiementSchema::FRAIS_TRANSACTION_DEVISE,
        PaiementSchema::REFERENCE_EXTERNE,
        PaiementSchema::REFERENCE_BANQUE,
        PaiementSchema::DATE_DEMANDE,
        PaiementSchema::DATE_AUTORISATION,
        PaiementSchema::DATE_CAPTURE,
        PaiementSchema::DATE_EXPIRATION,
        PaiementSchema::DONNEES_PAIEMENT,
        PaiementSchema::IP_ADDRESS,
        PaiementSchema::USER_AGENT,
    ];

    /**
     * Get the reservation that owns this paiement.
     *
     * @return BelongsTo<Reservation, $this>
     */
    public function reservation(): BelongsTo
    {
        /** @var BelongsTo<Reservation, $this> */
        return $this->belongsTo(Reservation::class, PaiementSchema::RESERVATION_ID, ReservationSchema::ID);
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this->statut_paiement === 'valide' || $this->statut_paiement === 'capture';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->statut_paiement === 'en_attente' || $this->statut_paiement === 'autorise';
    }

    /**
     * Check if payment is failed
     */
    public function isFailed(): bool
    {
        return in_array($this->statut_paiement, ['echec', 'refuse', 'expire', 'annule']);
    }

    /**
     * Check if payment is refunded
     */
    public function isRefunded(): bool
    {
        return $this->statut_paiement === 'rembourse';
    }

    /**
     * Check if payment is expired
     */
    public function isExpired(): bool
    {
        return $this->date_expiration && $this->date_expiration < now();
    }

    /**
     * Authorize payment
     */
    public function authorize(): bool
    {
        if ($this->statut_paiement !== 'en_attente') {
            return false;
        }

        $this->update([
            PaiementSchema::STATUT_PAIEMENT   => 'autorise',
            PaiementSchema::DATE_AUTORISATION => now(),
        ]);

        return true;
    }

    /**
     * Capture payment
     */
    public function capture(?\Money\Money $amount = null): bool
    {
        if ($this->statut_paiement !== 'autorise') {
            return false;
        }

        $this->update([
            PaiementSchema::STATUT_PAIEMENT => 'capture',
            PaiementSchema::DATE_CAPTURE    => now(),
            PaiementSchema::MONTANT_PAYE    => $amount ?? $this->montant_demande,
        ]);

        return true;
    }

    /**
     * Mark payment as failed
     */
    public function fail(?string $reason = null): bool
    {
        if ($this->isSuccessful()) {
            return false;
        }

        $this->update([
            PaiementSchema::STATUT_PAIEMENT  => 'echec',
            PaiementSchema::DONNEES_PAIEMENT => array_merge(
                $this->donnees_paiement ?? [],
                ['error_reason' => $reason]
            ),
        ]);

        return true;
    }

    /**
     * Get payment method display name
     */
    public function getMethodeDisplayAttribute(): string
    {
        return match ($this->methode_paiement) {
            'carte_bancaire' => 'Carte bancaire',
            'paypal'         => 'PayPal',
            'virement'       => 'Virement bancaire',
            'especes'        => 'Espèces',
            'cheque'         => 'Chèque',
            default          => $this->methode_paiement,
        };
    }

    /**
     * Get status display name
     */
    public function getStatutDisplayAttribute(): string
    {
        return match ($this->statut_paiement) {
            'en_attente' => 'En attente',
            'autorise'   => 'Autorisé',
            'capture'    => 'Capturé',
            'valide'     => 'Validé',
            'echec'      => 'Échec',
            'refuse'     => 'Refusé',
            'expire'     => 'Expiré',
            'annule'     => 'Annulé',
            'rembourse'  => 'Remboursé',
            default      => $this->statut_paiement,
        };
    }

    /**
     * Create a new factory instance for the model.
     */
    /** @phpstan-ignore-next-line class.notFound */
    protected static function newFactory(): \Database\Factories\Reservations\PaiementFactory
    {
        /** @phpstan-ignore-next-line class.notFound */
        return \Database\Factories\Reservations\PaiementFactory::new();
    }

    protected function casts(): array
    {
        return [
            PaiementSchema::MONTANT_DEMANDE   => AsMoney::class,
            PaiementSchema::MONTANT_PAYE      => AsMoney::class,
            PaiementSchema::FRAIS_TRANSACTION => AsMoney::class,
            PaiementSchema::DATE_DEMANDE      => 'datetime',
            PaiementSchema::DATE_AUTORISATION => 'datetime',
            PaiementSchema::DATE_CAPTURE      => 'datetime',
            PaiementSchema::DATE_EXPIRATION   => 'datetime',
            PaiementSchema::DONNEES_PAIEMENT  => 'array',
            PaiementSchema::CREATED_AT        => 'timestamp',
            PaiementSchema::UPDATED_AT        => 'timestamp',
        ];
    }
}
