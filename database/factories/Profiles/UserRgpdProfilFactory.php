<?php

declare(strict_types=1);

namespace Database\Factories\Profiles;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Infrastructure\Database\Models\Profiles\UserRgpdProfil;
use App\Infrastructure\Database\Schemas\Profiles\UserRgpdProfilSchema;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Database\Models\Profiles\UserRgpdProfil>
 */
final class UserRgpdProfilFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Infrastructure\Database\Models\Profiles\UserRgpdProfil>
     */
    protected $model = UserRgpdProfil::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            UserRgpdProfilSchema::USER_UUID_ORIGINAL         => $this->faker->uuid(),
            UserRgpdProfilSchema::NOM_SUBSTITUTION           => 'Utilisateur Supprimé',
            UserRgpdProfilSchema::PRENOM_SUBSTITUTION        => 'RGPD',
            UserRgpdProfilSchema::EMAIL_SUBSTITUTION         => $this->faker->unique()->safeEmail(),
            UserRgpdProfilSchema::DATE_SUPPRESSION_DEMANDEE  => $this->faker->dateTimeBetween('-2 years', '-1 month'),
            UserRgpdProfilSchema::DATE_SUPPRESSION_EFFECTIVE => $this->faker->dateTimeBetween('-1 month', 'now'),
            UserRgpdProfilSchema::RAISON_SUPPRESSION         => $this->faker->randomElement([
                'DROIT_OUBLI',
                'DEMANDE_CLIENT',
                'INACTIVITE',
                'VIOLATION_CGU',
            ]),
            UserRgpdProfilSchema::OPERATEUR_SUPPRESSION => $this->faker->randomElement([
                'Admin RGPD',
                'Sophie Manager',
                'Modération Auto',
                'Thomas Admin',
            ]),
            UserRgpdProfilSchema::COMMENTAIRE_INTERNE            => $this->faker->sentence(),
            UserRgpdProfilSchema::AVAIT_RESERVATIONS             => $this->faker->boolean(60),
            UserRgpdProfilSchema::NOMBRE_RESERVATIONS_HISTORIQUE => $this->faker->numberBetween(0, 50),
        ];
    }
}
