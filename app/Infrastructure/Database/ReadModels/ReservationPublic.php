<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use Carbon\Carbon;
use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\Model;
use App\Infrastructure\Database\Schemas\Reservations\ReservationPublicSchema;

/**
 * Modèle MongoDB pour les réservations
 *
 * @property string $reservation_uuid
 * @property string $numero_reservation
 * @property string $statut
 * @property string $film_id
 * @property string $seance_id
 * @property array<string, mixed> $film_info
 * @property array<string, mixed> $seance_info
 * @property array<string, mixed> $cinema_info
 * @property array<string, mixed> $client_info
 * @property array<string> $places_reservees
 * @property int $nb_places
 * @property float $tarif_unitaire
 * @property float $total
 * @property Carbon $date_reservation
 * @property Carbon $date_seance
 * @property array<string, mixed> $session_data
 * @property array<string, mixed> $metadata
 */
class ReservationPublic extends Model
{
    // Constantes pour les statuts
    public const STATUT_EN_COURS = 'en_cours';

    public const STATUT_CONFIRMEE = 'confirmee';

    public const STATUT_PAYEE = 'payee';

    public const STATUT_ANNULEE = 'annulee';

    public $connection = ReservationPublicSchema::CONNECTION;

    /** @var string */
    protected $collection = ReservationPublicSchema::COLLECTION;

    protected $fillable = [
        'reservation_uuid',
        'numero_reservation',
        'statut',
        'film_id',
        'seance_id',
        'film_info',
        'seance_info',
        'cinema_info',
        'client_info',
        'places_reservees',
        'nb_places',
        'tarif_unitaire',
        'total',
        'date_reservation',
        'date_seance',
        'session_data',
        'metadata',
    ];

    protected $casts = [
        'date_reservation' => 'datetime',
        'date_seance'      => 'datetime',
        'tarif_unitaire'   => 'float',
        'total'            => 'float',
        'nb_places'        => 'integer',
        'film_info'        => 'array',
        'seance_info'      => 'array',
        'cinema_info'      => 'array',
        'client_info'      => 'array',
        'places_reservees' => 'array',
        'session_data'     => 'array',
        'metadata'         => 'array',
    ];

    /**
     * Trouve une réservation par son UUID
     */
    public static function findByUuid(string $uuid): ?self
    {
        return static::where('reservation_uuid', $uuid)->first();
    }

    /**
     * Trouve une réservation par son numéro
     */
    public static function findByNumber(string $number): ?self
    {
        return static::where('numero_reservation', $number)->first();
    }

    /**
     * Réservations en cours pour une session
     */
    public static function findBySessionId(string $sessionId): ?self
    {
        return static::where('session_data.session_id', $sessionId)
            ->where('statut', self::STATUT_EN_COURS)
            ->first();
    }

    /**
     * Crée une nouvelle réservation depuis les données de session
     *
     * @param array<string, mixed> $sessionData
     */
    public static function createFromSession(array $sessionData, string $sessionId): self
    {
        return static::create([
            'film_id'   => $sessionData['film_id'],
            'seance_id' => $sessionData['seance_id'],
            'film_info' => [
                'titre' => $sessionData['titre'],
                'genre' => $sessionData['genre'] ?? null,
                'duree' => $sessionData['duree'] ?? null,
            ],
            'seance_info' => [
                'date_heure' => $sessionData['date_heure'],
                'salle'      => $sessionData['salle'],
                'version'    => $sessionData['version'] ?? 'VF',
                'qualite'    => $sessionData['qualite'] ?? 'Standard',
            ],
            'cinema_info' => [
                'nom'     => $sessionData['cinema_nom'] ?? '',
                'adresse' => $sessionData['cinema_adresse'] ?? '',
            ],
            'places_reservees' => $sessionData['places'] ?? [],
            'nb_places'        => $sessionData['nb_places'] ?? 1,
            'tarif_unitaire'   => floatval($sessionData['tarif'] ?? 12),
            'total'            => floatval($sessionData['total'] ?? (($sessionData['tarif'] ?? 12) * ($sessionData['nb_places'] ?? 1))),
            'date_seance'      => Carbon::parse($sessionData['date_heure']),
            'session_data'     => [
                'session_id' => $sessionId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
            'statut' => self::STATUT_EN_COURS,
        ]);
    }

    /**
     * Scope pour les réservations actives
     */
    public function scopeActive(mixed $query): mixed
    {
        return $query->whereIn('statut', [
            self::STATUT_CONFIRMEE,
            self::STATUT_PAYEE,
        ]);
    }

    /**
     * Scope pour les réservations confirmées
     */
    public function scopeConfirmees(mixed $query): mixed
    {
        return $query->where('statut', self::STATUT_CONFIRMEE);
    }

    /**
     * Vérifie si la réservation est modifiable
     */
    public function isModifiable(): bool
    {
        return in_array($this->statut, [
            self::STATUT_EN_COURS,
            self::STATUT_CONFIRMEE,
        ]);
    }

    /**
     * Vérifie si la réservation est payée
     */
    public function isPayee(): bool
    {
        return $this->statut === self::STATUT_PAYEE;
    }

    /**
     * Marque la réservation comme confirmée
     */
    public function confirmer(): void
    {
        $this->update([
            'statut'                     => self::STATUT_CONFIRMEE,
            'metadata.date_confirmation' => now(),
        ]);
    }

    /**
     * Marque la réservation comme payée
     *
     * @param array<string, mixed> $paymentData
     */
    public function marquerPayee(array $paymentData = []): void
    {
        $this->update([
            'statut'                 => self::STATUT_PAYEE,
            'metadata.date_paiement' => now(),
            'metadata.payment_data'  => $paymentData,
        ]);
    }

    /**
     * Annule la réservation
     */
    public function annuler(string $raison = ''): void
    {
        $this->update([
            'statut'                     => self::STATUT_ANNULEE,
            'metadata.date_annulation'   => now(),
            'metadata.raison_annulation' => $raison,
        ]);
    }

    /**
     * Met à jour les informations client
     *
     * @param array<string, mixed> $clientData
     */
    public function updateClientInfo(array $clientData): void
    {
        $this->update([
            'client_info' => [
                'nom'       => $clientData['nom'],
                'prenom'    => $clientData['prenom'],
                'email'     => $clientData['email'],
                'telephone' => $clientData['telephone'] ?? null,
            ],
        ]);
    }

    /**
     * Retourne les données formatées pour le QR Code
     *
     * @return array<string, mixed>
     */
    public function getQrCodeData(): array
    {
        return [
            'numero' => $this->numero_reservation,
            'uuid'   => $this->reservation_uuid,
            'film'   => $this->film_info['titre'] ?? '',
            'date'   => $this->date_seance->format('d/m/Y H:i'),
            'salle'  => $this->seance_info['salle'] ?? '',
            'places' => implode(', ', $this->places_reservees ?? []),
            'total'  => number_format($this->total, 2) . ' €',
        ];
    }

    /**
     * Retourne les données formatées pour le PDF
     *
     * @return array<string, mixed>
     */
    public function getPdfData(): array
    {
        return [
            'numeroReservation' => $this->numero_reservation,
            'film'              => $this->film_info['titre'] ?? '',
            'genre'             => $this->film_info['genre'] ?? '',
            'duree'             => $this->film_info['duree'] ?? '',
            'classification'    => 'Tous publics', // Par défaut
            'dateHeure'         => $this->date_seance->format('Y-m-d H:i:s'),
            'cinema'            => $this->cinema_info['nom'] ?? '',
            'adresse'           => $this->cinema_info['adresse'] ?? '',
            'salle'             => $this->seance_info['salle'] ?? '',
            'nbPlaces'          => $this->nb_places,
            'places'            => $this->places_reservees ?? [],
            'total'             => number_format($this->total, 2),
        ];
    }

    /**
     * Génère un UUID unique pour la réservation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (empty($reservation->reservation_uuid)) {
                $reservation->reservation_uuid = (string) Str::uuid();
            }

            if (empty($reservation->numero_reservation)) {
                $reservation->numero_reservation = 'RES-' . strtoupper(Str::random(8));
            }

            if (empty($reservation->date_reservation)) {
                $reservation->date_reservation = now();
            }

            if (empty($reservation->statut)) {
                $reservation->statut = self::STATUT_EN_COURS;
            }
        });
    }
}
