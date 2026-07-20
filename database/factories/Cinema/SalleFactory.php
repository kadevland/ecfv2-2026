<?php

declare(strict_types=1);

namespace Database\Factories\Cinema;

use App\Domain\Cinema\Enums\StatutSalle;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\Enums\QualiteProjection;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Infrastructure\Database\Models\Cinema\Salle;
use App\Infrastructure\Database\Schemas\Cinema\SalleSchema;

/**
 * @extends Factory<Salle>
 */
class SalleFactory extends Factory
{
    protected $model = Salle::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // RÈGLES MÉTIER COHÉRENTES :
        // 1. capacite_totale = nombre_rangees * places_par_rangee
        // 2. places_standard + places_pmr = capacite_totale

        $nombreRangees   = $this->faker->numberBetween(8, 25);
        $placesParRangee = $this->faker->numberBetween(12, 30);
        $capaciteTotale  = $nombreRangees * $placesParRangee;

        // PMR = 2-5% de la capacité totale (minimum 2 places)
        $placesPmr      = max(2, intval($capaciteTotale * $this->faker->randomFloat(2, 0.02, 0.05)));
        $placesStandard = $capaciteTotale - $placesPmr;

        return [
            SalleSchema::ID         => \App\Domain\Cinema\ValueObjects\SalleId::generate(),
            SalleSchema::CINEMA_KEY => null, // Will be set by seeder
            SalleSchema::CINEMA_ID  => null, // Will be set by seeder
            SalleSchema::NOM        => $this->faker->optional(0.6)
                ->randomElement(['Salle Premium', 'Salle VIP', 'Grande Salle', 'Salle Confort']),
            SalleSchema::CAPACITE_TOTALE    => $capaciteTotale,
            SalleSchema::NOMBRE_RANGEES     => $nombreRangees,
            SalleSchema::PLACES_PAR_RANGEE  => $placesParRangee,
            SalleSchema::PLACES_STANDARD    => $placesStandard,
            SalleSchema::PLACES_PMR         => $placesPmr,
            SalleSchema::QUALITE_PROJECTION => $this->generateQualiteProjection(),
            SalleSchema::QUALITE_SONORE     => $this->generateQualiteSonore(),
            SalleSchema::CLIMATISATION      => $this->faker->boolean(90),
            SalleSchema::ACCESSIBILITE_PMR  => $this->faker->boolean(95),
            SalleSchema::PLAN_SALLE         => $this->generateSeatingConfig($nombreRangees, $placesParRangee),
            SalleSchema::STATUT             => $this->faker->randomElement(StatutSalle::getValues()),
        ];
    }

    /**
     * Salle premium avec équipements haut de gamme
     */
    public function premium(): static
    {
        return $this->state(function (array $attributes) {
            $nombreRangees   = $this->faker->numberBetween(10, 15);
            $placesParRangee = $this->faker->numberBetween(14, 20);
            $capaciteTotale  = $nombreRangees * $placesParRangee;
            $placesPmr       = max(4, intval($capaciteTotale * 0.03)); // 3% PMR minimum
            $placesStandard  = $capaciteTotale - $placesPmr;

            return [
                SalleSchema::NOM                => 'Salle Premium',
                SalleSchema::CAPACITE_TOTALE    => $capaciteTotale,
                SalleSchema::NOMBRE_RANGEES     => $nombreRangees,
                SalleSchema::PLACES_PAR_RANGEE  => $placesParRangee,
                SalleSchema::PLACES_STANDARD    => $placesStandard,
                SalleSchema::PLACES_PMR         => $placesPmr,
                SalleSchema::QUALITE_PROJECTION => [QualiteProjection::IMAX->value, QualiteProjection::DOLBY_VISION->value, QualiteProjection::NUMERIQUE_4K->value],
                SalleSchema::QUALITE_SONORE     => [QualiteSonore::DOLBY_ATMOS->value, QualiteSonore::DTS_X->value],
                SalleSchema::ACCESSIBILITE_PMR  => true,
                SalleSchema::STATUT             => StatutSalle::ACTIVE->value,
            ];
        });
    }

    /**
     * Grande salle classique
     */
    public function grande(): static
    {
        return $this->state(function (array $attributes) {
            $nombreRangees   = $this->faker->numberBetween(15, 25);
            $placesParRangee = $this->faker->numberBetween(18, 30);
            $capaciteTotale  = $nombreRangees * $placesParRangee;
            $placesPmr       = max(6, intval($capaciteTotale * 0.025)); // 2.5% PMR minimum
            $placesStandard  = $capaciteTotale - $placesPmr;

            return [
                SalleSchema::NOM                => 'Grande Salle',
                SalleSchema::CAPACITE_TOTALE    => $capaciteTotale,
                SalleSchema::NOMBRE_RANGEES     => $nombreRangees,
                SalleSchema::PLACES_PAR_RANGEE  => $placesParRangee,
                SalleSchema::PLACES_STANDARD    => $placesStandard,
                SalleSchema::PLACES_PMR         => $placesPmr,
                SalleSchema::QUALITE_PROJECTION => [QualiteProjection::NUMERIQUE_4K->value, QualiteProjection::NUMERIQUE_2K->value],
                SalleSchema::QUALITE_SONORE     => [QualiteSonore::DOLBY_SURROUND->value, QualiteSonore::DTS->value],
                SalleSchema::STATUT             => StatutSalle::ACTIVE->value,
            ];
        });
    }

    /**
     * Petite salle intime
     */
    public function petite(): static
    {
        return $this->state(function (array $attributes) {
            $nombreRangees   = $this->faker->numberBetween(8, 12);
            $placesParRangee = $this->faker->numberBetween(12, 18);
            $capaciteTotale  = $nombreRangees * $placesParRangee;
            $placesPmr       = max(2, intval($capaciteTotale * 0.04)); // 4% PMR minimum
            $placesStandard  = $capaciteTotale - $placesPmr;

            return [
                SalleSchema::NOM                => 'Salle Intime',
                SalleSchema::CAPACITE_TOTALE    => $capaciteTotale,
                SalleSchema::NOMBRE_RANGEES     => $nombreRangees,
                SalleSchema::PLACES_PAR_RANGEE  => $placesParRangee,
                SalleSchema::PLACES_STANDARD    => $placesStandard,
                SalleSchema::PLACES_PMR         => $placesPmr,
                SalleSchema::QUALITE_PROJECTION => [QualiteProjection::NUMERIQUE_2K->value],
                SalleSchema::QUALITE_SONORE     => [QualiteSonore::DOLBY_SURROUND->value],
                SalleSchema::STATUT             => StatutSalle::ACTIVE->value,
            ];
        });
    }

    /**
     * Génère une configuration de sièges cohérente avec les données réelles
     */
    private function generateSeatingConfig(int $nombreRangees, int $placesParRangee): array
    {
        $config = [];
        for ($i = 1; $i <= $nombreRangees; $i++) {
            $config["rangee_{$i}"] = [
                'numero'        => $i,
                'nombre_sieges' => $placesParRangee,
                'type_sieges'   => $this->faker->randomElement(['standard', 'premium', 'confort']),
                'pmr'           => $i <= 2, // Les 2 premières rangées sont PMR
            ];
        }

        return $config;
    }

    /**
     * Génère les qualités de projection disponibles
     */
    private function generateQualiteProjection(): array
    {
        $qualites = QualiteProjection::getValues();

        return $this->faker->randomElements($qualites, rand(1, 3));
    }

    /**
     * Génère les qualités sonores disponibles
     */
    private function generateQualiteSonore(): array
    {
        $qualites = QualiteSonore::getValues();

        return $this->faker->randomElements($qualites, rand(1, 2));
    }
}
