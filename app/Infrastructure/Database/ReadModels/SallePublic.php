<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Infrastructure\Database\Schemas\Cinema\SallePublicSchema;

/**
 * Modèle MongoDB pour la collection salles_public
 * Collection optimisée pour l'affichage public des salles
 *
 * @property string $salle_id
 * @property string $cinema_id
 * @property string $nom
 * @property int $capacite_totale
 * @property string $statut
 */
class SallePublic extends Model
{
    use SoftDeletes;

    public $connection = SallePublicSchema::CONNECTION;

    protected $collection = SallePublicSchema::COLLECTION;

    protected $fillable = [
        '_id',
        'salle_id',
        'cinema_id',
        'nom',
        'capacite_totale',
        'nombre_rangees',
        'places_par_rangee',
        'places_standard',
        'places_pmr',
        'qualite_projection',
        'qualite_sonore',
        'accessibilite_pmr',
        'climatisation',
        'plan_salle',
        'statut',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'capacite_totale'    => 'integer',
        'nombre_rangees'     => 'integer',
        'places_par_rangee'  => 'integer',
        'places_standard'    => 'integer',
        'places_pmr'         => 'integer',
        'qualite_projection' => 'array',
        'qualite_sonore'     => 'array',
        'accessibilite_pmr'  => 'boolean',
        'climatisation'      => 'boolean',
        'plan_salle'         => 'array',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
        'deleted_at'         => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('statut', 'ACTIVE');
    }

    #[Scope]
    protected function byCinema(Builder $query, string $cinemaId): void
    {
        $query->where('cinema_id', $cinemaId);
    }

    #[Scope]
    protected function withQualiteProjection(Builder $query, string $qualite): void
    {
        $query->whereJsonContains('qualite_projection', $qualite);
    }

    #[Scope]
    protected function accessible(Builder $query): void
    {
        $query->where('accessibilite_pmr', true);
    }

    #[Scope]
    protected function minCapacity(Builder $query, int $minCapacity): void
    {
        $query->where('capacite_totale', '>=', $minCapacity);
    }

    #[Scope]
    protected function available(Builder $query): void
    {
        $query->where('statut', 'ACTIVE');
    }

    #[Scope]
    protected function search(Builder $query, string $search): void
    {
        $query->where('nom', 'like', "%{$search}%");
    }

    #[Scope]
    protected function orderByName(Builder $query): void
    {
        $query->orderBy('nom');
    }

    #[Scope]
    protected function orderByCapacity(Builder $query, string $direction = 'desc'): void
    {
        $query->orderBy('capacite_totale', $direction);
    }
}
