<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\MongoDB;

use Illuminate\Support\Carbon;
use MongoDB\Laravel\Eloquent\Model;
use App\Infrastructure\Database\Schemas\Cinema\FilmCatalogueSchema;

/**
 * Modèle MongoDB pour la collection films_catalogue (read-side)
 *
 * @property string $film_id
 * @property string $titre
 * @property ?string $titre_original
 * @property array<string> $realisateurs
 * @property array<string> $acteurs_principaux
 * @property array<string> $genres
 * @property ?array<string> $sous_titres
 * @property ?string $producteur
 * @property ?array<string> $images_additionnelles
 * @property ?array<string, mixed> $metadonnees_techniques
 * @property int $duree_minutes
 * @property string $classification
 * @property ?string $langue_originale
 * @property ?string $pays_origine
 * @property ?string $synopsis
 * @property Carbon $date_sortie
 * @property ?Carbon $date_fin_exploitation
 * @property ?float $note_critique
 * @property ?float $note_public
 * @property ?float $note_moyenne_avis
 * @property int $nombre_avis
 * @property ?string $affiche_url
 * @property ?string $bande_annonce_url
 * @property string $statut
 * @property bool $est_actif
 */
class FilmCatalogue extends Model
{
    /**
     * La connexion de base de données MongoDB
     */
    public $connection = FilmCatalogueSchema::CONNECTION;

    /**
     * La collection MongoDB
     */
    /** @var string */
    protected $collection = FilmCatalogueSchema::COLLECTION;

    /**
     * La clé primaire MongoDB
     */
    protected $primaryKey = '_id';

    /**
     * Les attributs castés
     */
    protected $casts = [
        'realisateurs'           => 'array',
        'acteurs_principaux'     => 'array',
        'genres'                 => 'array',
        'sous_titres'            => 'array',
        'images_additionnelles'  => 'array',
        'metadonnees_techniques' => 'array',
        'duree_minutes'          => 'integer',
        'note_critique'          => 'float',
        'note_public'            => 'float',
        'note_moyenne_avis'      => 'float',
        'nombre_avis'            => 'integer',
        'est_actif'              => 'boolean',
        'date_sortie'            => 'datetime',
        'date_fin_exploitation'  => 'datetime',
    ];

    /**
     * Les attributs fillables pour mass assignment
     */
    protected $fillable = [
        '_id',
        'film_id',
        'titre',
        'titre_original',
        'description',
        'synopsis',
        'genre',
        'genres',
        'realisateur',
        'realisateurs',
        'acteurs_principaux',
        'sous_titres',
        'producteur',
        'images_additionnelles',
        'metadonnees_techniques',
        'duree',
        'duree_minutes',
        'classification',
        'langue_originale',
        'pays_origine',
        'date_sortie',
        'date_fin_exploitation',
        'note_critique',
        'note_public',
        'note_moyenne',
        'note_moyenne_avis',
        'nombre_avis',
        'affiche_url',
        'bande_annonce_url',
        'statut',
        'statut_diffusion',
        'est_actif',
        'cinemas_diffusion',
        'prochaines_seances',
        'created_at',
        'updated_at',
    ];

    /**
     * Scope pour filtrer seulement les films actifs
     */
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Scope pour filtrer les films en cours d'exploitation
     */
    public function scopeEnExploitation($query)
    {
        $now = now();

        return $query->actif()
            ->where('date_sortie', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('date_fin_exploitation')
                    ->orWhere('date_fin_exploitation', '>=', $now);
            });
    }

    /**
     * Scope pour filtrer les films en diffusion (alias)
     */
    public function scopeEnDiffusion($query)
    {
        return $this->scopeEnExploitation($query);
    }

    /**
     * Scope pour filtrer avec séances disponibles
     */
    public function scopeWithAvailableSeances($query)
    {
        return $query->whereNotNull('cinemas_diffusion')
            ->where('cinemas_diffusion', '!=', []);
    }

    /**
     * Scope pour rechercher par genre
     */
    public function scopeByGenre($query, ?string $genre)
    {
        if (!$genre) {
            return $query;
        }

        return $query->where('genres', $genre);
    }

    /**
     * Scope pour rechercher par classification
     */
    public function scopeByClassification($query, ?string $classification)
    {
        if (!$classification) {
            return $query;
        }

        return $query->where('classification', $classification);
    }

    /**
     * Retourne la durée formatée
     */
    public function getFormattedDuration(): string
    {
        $hours   = intval($this->duree_minutes / 60);
        $minutes = $this->duree_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}min";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}min";
        }
    }

    /**
     * Vérifie si le film est actuellement à l'affiche
     */
    public function isInTheaters(): bool
    {
        if (!$this->est_actif) {
            return false;
        }

        $now = now();

        return $this->date_sortie <= $now &&
               (!$this->date_fin_exploitation || $this->date_fin_exploitation >= $now);
    }
}
