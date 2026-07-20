<?php

declare(strict_types=1);

namespace Database\Factories;

use Faker\Factory as FakerFactory;
use App\Domain\Shared\Enums\CodePays;
use App\Domain\Shared\ValueObjects\Email;
use App\Domain\Shared\ValueObjects\Address;
use App\Domain\Shared\ValueObjects\PhoneNumber;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Infrastructure\Database\Models\Cinema\Cinema;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;

/**
 * @extends Factory<Cinema>
 */
class CinemaFactory extends Factory
{
    protected $model = Cinema::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pays     = $this->faker->randomElement([CodePays::France, CodePays::Belgique]);
        $paysNom  = $pays === CodePays::France ? 'France' : 'Belgique';
        $paysCode = $pays === CodePays::France ? 'FR' : 'BE';

        // Utiliser le bon locale Faker selon le pays
        $localeFaker = $pays === CodePays::France
            ? FakerFactory::create('fr_FR')
            : FakerFactory::create('fr_BE');

        return [
            CinemaSchema::ID => \App\Domain\Cinema\ValueObjects\CinemaId::generate(),
            'nom'            => $this->faker->company(),
            'pays'           => $pays,
            'adresse'        => Address::fromArray([
                'rue'         => $localeFaker->streetAddress(),
                'ville'       => $localeFaker->city(),
                'code_postal' => $localeFaker->postcode(),
                'pays'        => $paysNom,
            ]),
            'telephone'   => PhoneNumber::tryFromTelephoneEtPays($localeFaker->phoneNumber(), $paysCode),
            'email'       => Email::tryFromString($this->faker->email()),
            'est_actif'   => true,
            'description' => $this->faker->optional(0.7)->paragraph(),
        ];
    }
}
