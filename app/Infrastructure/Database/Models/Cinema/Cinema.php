<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\Cinema;

use Illuminate\Support\Facades\DB;
use App\Infrastructure\Casts\AsEmail;
use App\Infrastructure\Casts\AsAddress;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Casts\AsCodePays;
use App\Infrastructure\Casts\AsIdentity;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Shared\ValueObjects\Address;
use App\Infrastructure\Casts\AsPhoneNumber;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Infrastructure\Casts\AsCoordonneesGps;
use App\Infrastructure\Casts\AsHorairesOuverture;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Infrastructure\Database\Concerns\HasUuidFinder;
use App\Infrastructure\Database\Schemas\Cinema\SalleSchema;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;

/**
 * @property int $db_id - PK auto-increment pour performances (technique)
 * @property CinemaId $id - Identifiant business/domain (DDD)
 * @property string $nom
 * @property \App\Domain\Shared\Enums\CodePays $pays - CodePays Enum
 * @property Address $adresse - JSONB vers Address Value Object
 * @property \App\Domain\Shared\ValueObjects\PhoneNumber|null $telephone - PhoneNumber VO
 * @property \App\Domain\Shared\ValueObjects\Email|null $email - Email Value Object
 * @property bool $est_actif
 * @property string|null $description
 * @property \App\Domain\Shared\ValueObjects\CoordonneesGps|null $coordonnees_gps - JSONB vers CoordonneesGps VO
 * @property \App\Domain\Shared\ValueObjects\HorairesOuverture|null $horaires_ouverture - JSONB vers HorairesOuverture VO
 */
final class Cinema extends Model
{
    /** @use HasFactory<\Database\Factories\CinemaFactory> */
    use HasFactory, HasUuidFinder;

    public const RELATION_SALLES = 'salles';

    protected $table = CinemaSchema::FULL_TABLE;

    protected $primaryKey = CinemaSchema::PRIMARY_KEY; // db_id (auto-increment)

    protected $fillable = [
        CinemaSchema::ID,          // Domain peut assigner l'UUID
        CinemaSchema::NOM,
        CinemaSchema::PAYS,
        CinemaSchema::ADRESSE,     // JSONB
        CinemaSchema::TELEPHONE,
        CinemaSchema::EMAIL,
        CinemaSchema::EST_ACTIF,
        CinemaSchema::DESCRIPTION,
        CinemaSchema::COORDONNEES_GPS, // JSONB
        CinemaSchema::HORAIRES_OUVERTURE, // JSONB
    ];

    /**
     * Get the salles for this cinema.
     *
     * @return HasMany<Salle, $this>
     */
    public function salles(): HasMany
    {
        /** @var HasMany<Salle, $this> */
        return $this->hasMany(Salle::class, SalleSchema::CINEMA_KEY, CinemaSchema::PRIMARY_KEY);
    }

    // ============================================
    // Accessors pour compatibilité avec les vues
    // ============================================

    /**
     * Accessor pour ville - extrait du JSONB adresse
     */
    public function getVilleAttribute(): string
    {
        return $this->adresse?->ville ?? 'N/A';
    }

    /**
     * Accessor pour code_postal - extrait du JSONB adresse
     */
    public function getCodePostalAttribute(): string
    {
        return $this->adresse?->codePostal ?? '00000';
    }

    /**
     * Accessor pour rue - extrait du JSONB adresse
     */
    public function getRueAttribute(): string
    {
        return $this->adresse?->rue ?? 'N/A';
    }

    /**
     * Nom de la colonne UUID - méthode statique optimisée
     */
    protected static function getUuidColumnName(): string
    {
        return CinemaSchema::ID;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \Database\Factories\CinemaFactory
    {
        return \Database\Factories\CinemaFactory::new();
    }

    // ============================================
    // Scopes pour queries JSON sur adresse
    // ============================================

    /**
     * Scope: Filter by ville dans le JSON adresse
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function whereVille(Builder $query, string $ville): void
    {
        $query->where(CinemaSchema::ADRESSE . '->' . CinemaSchema::ADRESSE_VILLE, $ville);
    }

    /**
     * Scope: Filter by code_postal dans le JSON adresse
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function whereCodePostal(Builder $query, string $codePostal): void
    {
        $query->where(CinemaSchema::ADRESSE . '->' . CinemaSchema::ADRESSE_CODE_POSTAL, $codePostal);
    }

    /**
     * Scope: Filter by rue dans le JSON adresse
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function whereRue(Builder $query, string $rue): void
    {
        $query->whereLike(CinemaSchema::ADRESSE . '->' . CinemaSchema::ADRESSE_RUE, "%{$rue}%");
    }

    /**
     * Scope: Filter by pays (colonne séparée)
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function wherePays(Builder $query, string $pays): void
    {
        $query->where(CinemaSchema::PAYS, $pays);
    }

    /**
     * Scope: Filter by active status
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where(CinemaSchema::EST_ACTIF, true);
    }

    /**
     * Scope: Search in ville or rue
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function searchAdresse(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {

            $q->whereLike(CinemaSchema::ADRESSE . '->' . CinemaSchema::ADRESSE_VILLE, "%{$search}%")
                ->orWhereLike(CinemaSchema::ADRESSE . '->' . CinemaSchema::ADRESSE_RUE, "%{$search}%");
            // ->orWhereJsonContains(CinemaSchema::ADRESSE . '->' . CinemaSchema::ADRESSE_RUE, 'ILIKE', "%{$search}%");
        });
    }

    /**
     * Scope: Filter by région (utilise index GIN sur JSON)
     *
     * @param Builder<Cinema> $query
     * @param array<string> $villes
     */
    #[Scope]
    protected function whereRegion(Builder $query, array $villes): void
    {
        $query->whereIn(CinemaSchema::ADRESSE . '->' . CinemaSchema::ADRESSE_VILLE, $villes);
    }

    protected function casts(): array
    {
        return [
            CinemaSchema::ID                 => AsIdentity::class . ':' . CinemaId::class, // uuid -> CinemaId
            CinemaSchema::ADRESSE            => AsAddress::class, // JSONB -> Address VO
            CinemaSchema::TELEPHONE          => AsPhoneNumber::class, // string E164 -> PhoneNumber VO
            CinemaSchema::EMAIL              => AsEmail::class, // string -> Email VO
            CinemaSchema::PAYS               => AsCodePays::class, // string -> CodePays Enum
            CinemaSchema::EST_ACTIF          => 'boolean',
            CinemaSchema::DESCRIPTION        => 'string',
            CinemaSchema::COORDONNEES_GPS    => AsCoordonneesGps::class, // JSONB -> CoordonneesGps VO
            CinemaSchema::HORAIRES_OUVERTURE => AsHorairesOuverture::class, // JSONB -> HorairesOuverture VO
        ];
    }

    /**
     * Classe d'identité pour ce modèle
     */
    protected function getUuidIdentityClass(): string
    {
        return CinemaId::class;
    }

    // ============================================
    // Business Scopes - Encapsulation des colonnes
    // ============================================

    /**
     * Scope: Filter by active status (alias français pour active())
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function whereActif(Builder $query): void
    {
        $query->where(CinemaSchema::EST_ACTIF, true);
    }

    /**
     * Scope: Filter by nom
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function whereNom(Builder $query, string $nom): void
    {
        $query->where(CinemaSchema::NOM, $nom);
    }

    // ============================================
    // GPS Scopes - Requêtes géospatiales
    // ============================================

    /**
     * Scope: Filter cinemas that have GPS coordinates
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function hasGpsCoordinates(Builder $query): void
    {
        $query->whereNotNull(CinemaSchema::COORDONNEES_GPS);
    }

    /**
     * Scope: Filter by latitude range (JSON query)
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function whereLatitudeBetween(Builder $query, float $minLat, float $maxLat): void
    {

        $column = sprintf(
            "(%s->>'%s')::float",
            CinemaSchema::COORDONNEES_GPS,
            CinemaSchema::GPS_LATITUDE
        );

        $query->whereBetween(DB::raw($column), [$minLat, $maxLat]);

    }

    /**
     * Scope: Filter by longitude range (JSON query)
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function whereLongitudeBetween(Builder $query, float $minLng, float $maxLng): void
    {

        $column = sprintf(
            "(%s->>'%s')::float",
            CinemaSchema::COORDONNEES_GPS,
            CinemaSchema::GPS_LONGITUDE
        );

        $query->whereBetween(DB::raw($column), [$minLng, $maxLng]);
    }

    /**
     * Scope: Filter cinemas in France métropolitaine approximative
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function inFranceMetropolitaine(Builder $query): void
    {
        $query->hasGpsCoordinates()
            ->whereLatitudeBetween(41.0, 51.5)
            ->whereLongitudeBetween(-5.5, 10.0);
    }

    /**
     * Scope: Filter cinemas in Belgium approximative
     *
     * @param Builder<Cinema> $query
     */
    #[Scope]
    protected function inBelgique(Builder $query): void
    {
        $query->hasGpsCoordinates()
            ->whereLatitudeBetween(49.4, 51.6)
            ->whereLongitudeBetween(2.5, 6.5);
    }
}
