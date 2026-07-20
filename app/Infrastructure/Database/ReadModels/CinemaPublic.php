<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Infrastructure\Database\Schemas\Cinema\CinemaPublicSchema;

/**
 * Modèle MongoDB pour la collection cinemas_public
 * Collection optimisée pour l'affichage public des cinémas
 *
 * @property string $cinema_id
 * @property string $nom
 * @property string $adresse
 * @property string $ville
 * @property string $code_postal
 * @property string|array<mixed> $telephone
 * @property string|array<mixed> $email
 * @property int $nombre_salles
 * @property array<string, mixed> $horaires_ouverture
 * @property bool $accessibilite_pmr
 * @property array<string, mixed> $salles
 * @property array<string, mixed> $services
 * @property string|null $description
 * @property string $statut
 * @property float|null $latitude
 * @property float|null $longitude
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class CinemaPublic extends Model
{
    use SoftDeletes;

    public $connection = CinemaPublicSchema::CONNECTION;

    protected $collection = CinemaPublicSchema::COLLECTION;

    protected $fillable = [
        CinemaPublicSchema::CINEMA_ID,
        CinemaPublicSchema::NOM,
        CinemaPublicSchema::DESCRIPTION,
        CinemaPublicSchema::ADRESSE,
        CinemaPublicSchema::VILLE,
        CinemaPublicSchema::CODE_POSTAL,
        CinemaPublicSchema::PAYS,
        CinemaPublicSchema::TELEPHONE,
        CinemaPublicSchema::EMAIL,
        CinemaPublicSchema::HORAIRES_OUVERTURE,
        CinemaPublicSchema::SERVICES,
        CinemaPublicSchema::LATITUDE,
        CinemaPublicSchema::LONGITUDE,
        CinemaPublicSchema::NOMBRE_SALLES,
        CinemaPublicSchema::SALLES,
        CinemaPublicSchema::TOTAL_PLACES,
        CinemaPublicSchema::STATUT,
    ];

    protected $casts = [
        CinemaPublicSchema::HORAIRES_OUVERTURE => 'array',
        CinemaPublicSchema::SERVICES           => 'array',
        CinemaPublicSchema::LATITUDE           => 'float',
        CinemaPublicSchema::LONGITUDE          => 'float',
        CinemaPublicSchema::NOMBRE_SALLES      => 'integer',
        CinemaPublicSchema::SALLES             => 'array',
        CinemaPublicSchema::TOTAL_PLACES       => 'integer',
        CinemaPublicSchema::CREATED_AT         => 'datetime',
        CinemaPublicSchema::UPDATED_AT         => 'datetime',
        CinemaPublicSchema::DELETED_AT         => 'datetime',
    ];

    protected $dates = [
        CinemaPublicSchema::CREATED_AT,
        CinemaPublicSchema::UPDATED_AT,
        CinemaPublicSchema::DELETED_AT,
    ];

    /**
     * Récupère les cinémas actifs
     */
    public function scopeActif($query)
    {
        return $query->where(CinemaPublicSchema::STATUT, 'actif');
    }

    /**
     * Récupère les cinémas par location/ville
     */
    public function scopeByLocation($query, ?string $location = null)
    {
        if ($location) {
            return $query->where(CinemaPublicSchema::VILLE, 'like', "%{$location}%");
        }

        return $query;
    }

    /**
     * Récupère les cinémas par ville
     */
    public function scopeByVille($query, string $ville)
    {
        return $query->where(CinemaPublicSchema::VILLE, $ville);
    }

    /**
     * Récupère les cinémas avec un service spécifique
     */
    public function scopeWithService($query, string $service)
    {
        return $query->where(CinemaPublicSchema::SERVICES, $service);
    }

    /**
     * Recherche par nom de cinéma
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(CinemaPublicSchema::NOM, 'like', "%{$search}%");
    }

    /**
     * @return array<string, mixed>
     */
    public function getFormattedHoraires(): array
    {
        return $this->horaires_ouverture ?? [];
    }

    public function hasAccessibilityPmr(): bool
    {
        return $this->accessibilite_pmr ?? false;
    }
}
