<?php

declare(strict_types=1);

namespace Database\Factories\Profiles;

use App\Domain\User\ValueObjects\UserProfilId;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Infrastructure\Database\Models\Profiles\UserProfil;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Database\Models\Profiles\UserProfil>
 */
final class UserProfilFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Infrastructure\Database\Models\Profiles\UserProfil>
     */
    protected $model = UserProfil::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender    = $this->faker->randomElement(['M', 'F']);
        $firstName = $gender === 'M' ? $this->faker->firstNameMale() : $this->faker->firstNameFemale();

        return [
            UserProfilSchema::ID             => UserProfilId::generate(),
            UserProfilSchema::PRENOM         => $firstName,
            UserProfilSchema::NOM            => $this->faker->lastName(),
            UserProfilSchema::EMAIL          => $this->faker->unique()->safeEmail(),
            UserProfilSchema::TELEPHONE      => $this->faker->optional(0.8)->phoneNumber(),
            UserProfilSchema::DATE_NAISSANCE => $this->faker->optional(0.9)->dateTimeBetween('-80 years', '-18 years'),
            UserProfilSchema::SEXE           => $this->faker->optional(0.7)->randomElement(['M', 'F', 'AUTRE', 'NON_SPECIFIE']),
            UserProfilSchema::ADRESSE        => $this->generateAdresse(),
            UserProfilSchema::PREFERENCES    => $this->generatePreferences(),
            UserProfilSchema::NEWSLETTER     => $this->faker->boolean(30), // 30% chance de newsletter
        ];
    }

    /**
     * Create profile for French users.
     */
    public function french(): static
    {
        return $this->state(fn (): array => [
            UserProfilSchema::ADRESSE => [
                'rue'         => $this->faker->streetAddress(),
                'ville'       => $this->faker->city(),
                'code_postal' => $this->faker->regexify('[0-9]{5}'),
                'pays'        => 'France',
                'complement'  => $this->faker->optional(0.3)->secondaryAddress(),
            ],
            UserProfilSchema::PREFERENCES => [
                ...$this->generatePreferences(),
                'langue' => 'fr',
            ],
        ]);
    }

    /**
     * Create profile for Belgian users.
     */
    public function belgian(): static
    {
        return $this->state(fn (): array => [
            UserProfilSchema::ADRESSE => [
                'rue'         => $this->faker->streetAddress(),
                'ville'       => $this->faker->randomElement(['Bruxelles', 'Anvers', 'Gand', 'Liège', 'Namur']),
                'code_postal' => $this->faker->regexify('[1-9][0-9]{3}'),
                'pays'        => 'Belgique',
                'complement'  => $this->faker->optional(0.2)->secondaryAddress(),
            ],
            UserProfilSchema::PREFERENCES => [
                ...$this->generatePreferences(),
                'langue' => $this->faker->randomElement(['fr', 'nl']),
            ],
        ]);
    }

    /**
     * Create anonymized RGPD profile.
     */
    public function rgpdDeleted(): static
    {
        return $this->state(fn (): array => [
            UserProfilSchema::PRENOM         => 'SUPPRIMÉ',
            UserProfilSchema::NOM            => 'SUPPRIMÉ',
            UserProfilSchema::TELEPHONE      => null,
            UserProfilSchema::DATE_NAISSANCE => null,
            UserProfilSchema::SEXE           => null,
            UserProfilSchema::ADRESSE        => [
                'rue'         => 'SUPPRIMÉ',
                'ville'       => 'SUPPRIMÉ',
                'code_postal' => '00000',
                'pays'        => 'SUPPRIMÉ',
            ],
            UserProfilSchema::PREFERENCES => [
                'newsletter'            => false,
                'notifications_email'   => false,
                'notifications_sms'     => false,
                'notifications_push'    => false,
                'langue'                => 'fr',
                'theme'                 => 'auto',
                'marketing_partenaires' => false,
                'rappels_seances'       => false,
            ],
            UserProfilSchema::NEWSLETTER => false,
        ]);
    }

    /**
     * Create minimal profile (for testing).
     */
    public function minimal(): static
    {
        return $this->state(fn (): array => [
            UserProfilSchema::TELEPHONE      => null,
            UserProfilSchema::DATE_NAISSANCE => null,
            UserProfilSchema::SEXE           => null,
            UserProfilSchema::ADRESSE        => null,
            UserProfilSchema::PREFERENCES    => [
                'langue' => 'fr',
                'theme'  => 'auto',
            ],
            UserProfilSchema::NEWSLETTER => false,
        ]);
    }

    /**
     * Generate French address structure.
     *
     * @return array<string, mixed>
     */
    private function generateAdresse(): array
    {
        $isFrench = $this->faker->boolean(80); // 80% French addresses

        if ($isFrench) {
            return [
                'rue'         => $this->faker->streetAddress(),
                'ville'       => $this->faker->city(),
                'code_postal' => $this->faker->regexify('[0-9]{5}'),
                'pays'        => 'France',
                'complement'  => $this->faker->optional(0.3)->secondaryAddress(),
            ];
        }

        // Belgian addresses for diversity
        return [
            'rue'         => $this->faker->streetAddress(),
            'ville'       => $this->faker->randomElement(['Bruxelles', 'Anvers', 'Gand', 'Liège', 'Namur']),
            'code_postal' => $this->faker->regexify('[1-9][0-9]{3}'),
            'pays'        => 'Belgique',
            'complement'  => $this->faker->optional(0.2)->secondaryAddress(),
        ];
    }

    /**
     * Generate user preferences.
     *
     * @return array<string, mixed>
     */
    private function generatePreferences(): array
    {
        return [
            'newsletter'            => $this->faker->boolean(30),
            'notifications_email'   => $this->faker->boolean(70),
            'notifications_sms'     => $this->faker->boolean(40),
            'notifications_push'    => $this->faker->boolean(60),
            'langue'                => $this->faker->randomElement(['fr', 'en', 'nl']),
            'theme'                 => $this->faker->randomElement(['auto', 'light', 'dark']),
            'marketing_partenaires' => $this->faker->boolean(20),
            'rappels_seances'       => $this->faker->boolean(80),
        ];
    }
}
