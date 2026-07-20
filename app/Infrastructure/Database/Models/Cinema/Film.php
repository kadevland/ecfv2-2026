<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Cinema;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Schemas\Cinema\FilmSchema;
use App\Infrastructure\Database\Schemas\Cinema\SeanceSchema;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $db_id
 * @property string $uuid
 * @property string $titre
 * @property string|null $titre_original
 * @property string|null $synopsis
 * @property array<string> $genres
 * @property int $duree_minutes
 * @property \Illuminate\Support\Carbon $date_sortie
 * @property \Illuminate\Support\Carbon|null $date_fin_exploitation
 * @property string $pays_origine
 * @property string $langue_originale
 * @property array<string>|null $sous_titres
 * @property string $classification
 * @property array<string> $realisateurs
 * @property array<string>|null $acteurs_principaux
 * @property string|null $producteur
 * @property string|null $affiche_url
 * @property string|null $bande_annonce_url
 * @property array<string>|null $images_additionnelles
 * @property float|null $note_critique
 * @property float|null $note_public
 * @property float|null $note_moyenne_avis
 * @property int $nombre_avis
 * @property string $statut
 * @property bool $est_actif
 * @property array<string, mixed>|null $metadonnees_techniques
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Seance> $seances
 */
final class Film extends Model
{
    /** @use HasFactory<\Database\Factories\Cinema\FilmFactory> */
    use HasFactory;

    protected $connection = FilmSchema::CONNECTION;

    protected $table = FilmSchema::FULL_TABLE;

    protected $primaryKey = FilmSchema::PRIMARY_KEY; // db_id auto-increment

    protected $fillable = [
        FilmSchema::ID,
        FilmSchema::TITRE,
        FilmSchema::TITRE_ORIGINAL,
        FilmSchema::SYNOPSIS,
        FilmSchema::GENRES,
        FilmSchema::DUREE_MINUTES,
        FilmSchema::DATE_SORTIE,
        FilmSchema::DATE_FIN_EXPLOITATION,
        FilmSchema::PAYS_ORIGINE,
        FilmSchema::LANGUE_ORIGINALE,
        FilmSchema::SOUS_TITRES,
        FilmSchema::CLASSIFICATION,
        FilmSchema::REALISATEURS,
        FilmSchema::ACTEURS_PRINCIPAUX,
        FilmSchema::PRODUCTEUR,
        FilmSchema::AFFICHE_URL,
        FilmSchema::BANDE_ANNONCE_URL,
        FilmSchema::IMAGES_ADDITIONNELLES,
        FilmSchema::NOTE_CRITIQUE,
        FilmSchema::NOTE_PUBLIC,
        FilmSchema::NOTE_MOYENNE_AVIS,
        FilmSchema::NOMBRE_AVIS,
        FilmSchema::STATUT,
        FilmSchema::EST_ACTIF,
        FilmSchema::METADONNEES_TECHNIQUES,
    ];

    /**
     * Get the seances for this film.
     *
     * @return HasMany<Seance, $this>
     */
    public function seances(): HasMany
    {
        /** @var HasMany<Seance, $this> */
        return $this->hasMany(Seance::class, SeanceSchema::FILM_KEY, FilmSchema::PRIMARY_KEY);
    }

    /**
     * Check if film is currently in theaters
     */
    public function isInTheaters(): bool
    {
        $now = now();

        return $this->est_actif
            && $this->date_sortie <= $now
            && ($this->date_fin_exploitation === null || $this->date_fin_exploitation >= $now);
    }

    /**
     * Get formatted duration (ex: "2h 15min")
     */
    public function getFormattedDurationAttribute(): string
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
     * Get director name
     */
    public function getPrimaryDirectorAttribute(): ?string
    {
        return isset($this->realisateurs[0]) ? $this->realisateurs[0] : null;
    }

    /**
     * Update average rating based on all reviews
     * Note: This would be used when implementing a review system
     */
    public function updateAverageRating(float $newRating): void
    {
        // This method would be implemented when we add a reviews system
        // For now, we just update the public rating
        $this->update([
            FilmSchema::NOTE_PUBLIC => $newRating,
        ]);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \Database\Factories\Cinema\FilmFactory
    {
        return \Database\Factories\Cinema\FilmFactory::new();
    }

    protected function casts(): array
    {
        return [
            FilmSchema::GENRES                 => 'array',
            FilmSchema::REALISATEURS           => 'array',
            FilmSchema::ACTEURS_PRINCIPAUX     => 'array',
            FilmSchema::SOUS_TITRES            => 'array',
            FilmSchema::DUREE_MINUTES          => 'integer',
            FilmSchema::NOMBRE_AVIS            => 'integer',
            FilmSchema::DATE_SORTIE            => 'date',
            FilmSchema::DATE_FIN_EXPLOITATION  => 'datetime',
            FilmSchema::NOTE_CRITIQUE          => 'decimal:1',
            FilmSchema::NOTE_PUBLIC            => 'decimal:1',
            FilmSchema::NOTE_MOYENNE_AVIS      => 'decimal:1',
            FilmSchema::EST_ACTIF              => 'boolean',
            FilmSchema::IMAGES_ADDITIONNELLES  => 'array',
            FilmSchema::METADONNEES_TECHNIQUES => 'array',
            FilmSchema::CREATED_AT             => 'datetime',
            FilmSchema::UPDATED_AT             => 'datetime',
        ];
    }

    #[Scope]
    public function whereInTheaters(Builder $query): void
    {
        $now = now();

        $query->where(FilmSchema::EST_ACTIF, true)
            ->where(FilmSchema::DATE_SORTIE, '<=', $now)
            ->where(function ($query) use ($now) {
                $query->whereNull(FilmSchema::DATE_FIN_EXPLOITATION)
                    ->orWhere(FilmSchema::DATE_FIN_EXPLOITATION, '>=', $now);
            });
    }
}
