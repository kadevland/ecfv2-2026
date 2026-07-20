<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\MongoDB;

use Exception;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Modèle MongoDB pour la collection reservations (read-side)
 *
 * @property string $reservation_id
 * @property string $client_id
 * @property string $client_nom
 * @property string $client_email
 * @property string $seance_id
 * @property string $film_id
 * @property string $film_titre
 * @property string $cinema_id
 * @property string $cinema_nom
 * @property string $salle_nom
 * @property string $date_seance
 * @property string $heure_seance
 * @property array $places
 * @property float $prix_total
 * @property string $statut
 * @property ?string $numero_confirmation
 * @property ?string $qr_code
 * @property string $created_at
 * @property string $updated_at
 */
class ReservationRead extends Model
{
    /**
     * La connexion de base de données MongoDB
     */
    protected $connection = 'mongodb';

    /**
     * La collection MongoDB
     */
    protected $collection = 'reservations';

    /**
     * La clé primaire MongoDB
     */
    protected $primaryKey = '_id';

    /**
     * Les attributs castés
     */
    protected $casts = [
        'places'      => 'array',
        'prix_total'  => 'float',
        'date_seance' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * Les attributs fillables pour mass assignment
     */
    protected $fillable = [
        'reservation_id',
        'client_id',
        'client_nom',
        'client_email',
        'seance_id',
        'film_id',
        'film_titre',
        'cinema_id',
        'cinema_nom',
        'salle_nom',
        'date_seance',
        'heure_seance',
        'places',
        'prix_total',
        'statut',
        'numero_confirmation',
        'qr_code',
    ];

    /**
     * Scope pour filtrer par client
     */
    public function scopeByClient($query, string $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope pour filtrer par cinéma
     */
    public function scopeByCinema($query, string $cinemaId)
    {
        return $query->where('cinema_id', $cinemaId);
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeByStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour les réservations confirmées
     */
    public function scopeConfirmees($query)
    {
        return $query->where('statut', 'confirmee');
    }

    /**
     * Scope pour les réservations futures
     */
    public function scopeFutures($query)
    {
        return $query->where('date_seance', '>=', now());
    }

    /**
     * Scope pour les réservations passées
     */
    public function scopePassees($query)
    {
        return $query->where('date_seance', '<', now());
    }

    /**
     * Retourne le nombre de places réservées
     */
    public function getNombrePlaces(): int
    {
        return count($this->places ?? []);
    }

    /**
     * Vérifie si la réservation peut être annulée
     */
    public function canBeCancelled(): bool
    {
        if ($this->statut !== 'confirmee') {
            return false;
        }

        // Peut être annulée jusqu'à 1h avant la séance
        $seanceTime = $this->date_seance;
        if (!$seanceTime) {
            return false;
        }

        // Convert to Carbon if it's a string
        if (is_string($seanceTime)) {
            try {
                $seanceTime = \Carbon\Carbon::parse($seanceTime);
            } catch (Exception) {
                return false;
            }
        }

        return $seanceTime->copy()->subHour()->isFuture();
    }

    /**
     * Retourne les informations de places formatées
     */
    public function getFormattedPlaces(): string
    {
        $places = $this->places ?? [];
        if (empty($places)) {
            return 'Aucune place';
        }

        return implode(', ', array_map(function ($place) {
            return "Rang {$place['rang']} - Siège {$place['numero']}";
        }, $places));
    }
}
