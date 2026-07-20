<?php

declare(strict_types=1);

namespace Database\Seeders\Cinema;

use Illuminate\Database\Seeder;
use App\Domain\Shared\Enums\CodePays;
use App\Domain\Shared\ValueObjects\Email;
use App\Domain\Shared\ValueObjects\Address;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Shared\ValueObjects\PhoneNumber;
use App\Domain\Shared\ValueObjects\CoordonneesGps;
use App\Domain\Shared\ValueObjects\HoraireJournalier;
use App\Domain\Shared\ValueObjects\HorairesOuverture;
use App\Infrastructure\Database\Models\Cinema\Cinema;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;

final class CinemaSeeder extends Seeder
{
    public function run(): void
    {
        $this->createRealCinemas();
        $this->createFakeCinemas();
    }

    private function createRealCinemas(): void
    {
        // Cinéma Paris Champs-Élysées
        Cinema::create([
            CinemaSchema::ID => CinemaId::generate(),
            'nom'            => 'Cinéma Champs-Élysées',
            'pays'           => CodePays::France,
            'adresse'        => Address::creer(
                rue: '123 Avenue des Champs-Élysées',
                ville: 'Paris',
                codePostal: '75008',
                pays: 'France'
            ),
            'coordonnees_gps'    => CoordonneesGps::creer(48.8566, 2.3522),
            'telephone'          => PhoneNumber::tryFromE164('+33142565656'),
            'email'              => Email::tryFromString('contact@cinema-champs.fr'),
            'est_actif'          => true,
            'description'        => 'Cinéma moderne au cœur de Paris, près des Champs-Élysées.',
            'horaires_ouverture' => $this->createHorairesStandard(),
        ]);

        // Cinéma Lyon Bellecour
        Cinema::create([
            CinemaSchema::ID => CinemaId::generate(),
            'nom'            => 'Cinéma Bellecour',
            'pays'           => CodePays::France,
            'adresse'        => Address::creer(
                rue: '15 Place Bellecour',
                ville: 'Lyon',
                codePostal: '69002',
                pays: 'France'
            ),
            'coordonnees_gps'    => CoordonneesGps::creer(45.7578, 4.8320),
            'telephone'          => PhoneNumber::tryFromE164('+33472414545'),
            'email'              => Email::tryFromString('info@cinema-bellecour.fr'),
            'est_actif'          => true,
            'description'        => 'Le cinéma historique de Lyon, place Bellecour.',
            'horaires_ouverture' => $this->createHorairesTypique(),
        ]);

        // Cinéma Bruxelles Grand Place
        Cinema::create([
            CinemaSchema::ID => CinemaId::generate(),
            'nom'            => 'Cinéma Grand Place',
            'pays'           => CodePays::Belgique,
            'adresse'        => Address::creer(
                rue: '8 Grand Place',
                ville: 'Bruxelles',
                codePostal: '1000',
                pays: 'Belgique'
            ),
            'coordonnees_gps'    => CoordonneesGps::creer(50.8467, 4.3525),
            'telephone'          => PhoneNumber::tryFromE164('+3225123456'),
            'email'              => Email::tryFromString('contact@cinema-grandplace.be'),
            'est_actif'          => true,
            'description'        => 'Cinéma authentique au centre historique de Bruxelles.',
            'horaires_ouverture' => $this->createHorairesWeekend(),
        ]);

        // Cinéma Marseille Vieux Port
        Cinema::create([
            CinemaSchema::ID => CinemaId::generate(),
            'nom'            => 'Cinéma Vieux Port',
            'pays'           => CodePays::France,
            'adresse'        => Address::creer(
                rue: '42 Quai du Port',
                ville: 'Marseille',
                codePostal: '13002',
                pays: 'France'
            ),
            'coordonnees_gps'    => CoordonneesGps::creer(43.2951, 5.3745),
            'telephone'          => PhoneNumber::tryFromE164('+33491789012'),
            'email'              => Email::tryFromString('accueil@cinema-vieuxport.fr'),
            'est_actif'          => true,
            'description'        => 'Vue imprenable sur le Vieux Port de Marseille.',
            'horaires_ouverture' => $this->createHorairesStandard(),
        ]);

        // Cinéma inactif pour tests
        Cinema::create([
            CinemaSchema::ID => CinemaId::generate(),
            'nom'            => 'Ancien Cinéma Fermé',
            'pays'           => CodePays::France,
            'adresse'        => Address::creer(
                rue: '99 Rue de la Fermeture',
                ville: 'Lille',
                codePostal: '59000',
                pays: 'France'
            ),
            'coordonnees_gps' => null, // Sans GPS
            'telephone'       => null,
            'email'           => null,
            'est_actif'       => false,
            'description'     => 'Cinéma fermé pour renovation.',
        ]);
    }

    private function createFakeCinemas(): void
    {
        // Créer 5 cinémas générés avec factory
        Cinema::factory()->count(5)->create();
    }

    private function createHorairesStandard(): HorairesOuverture
    {
        // Horaires 9h-12h30 (+2h) et 15h-22h30 (+2h) tous les jours
        $horaireStandard = HoraireJournalier::create(
            debutMatin: '09:00',
            finMatin: '12:30',
            dureeMaxSeanceMatin: 120,  // 2h
            debutApres: '15:00',
            finApres: '22:30',
            dureeMaxSeanceApres: 120   // 2h
        );

        return new HorairesOuverture(
            $horaireStandard, // Lundi
            $horaireStandard, // Mardi
            $horaireStandard, // Mercredi
            $horaireStandard, // Jeudi
            $horaireStandard, // Vendredi
            $horaireStandard, // Samedi
            $horaireStandard  // Dimanche
        );
    }

    private function createHorairesTypique(): HorairesOuverture
    {
        // Horaires 9h-12h30 (+2h) et 15h-22h30 (+2h) tous les jours
        $horaireStandard = HoraireJournalier::create(
            debutMatin: '09:00',
            finMatin: '12:30',
            dureeMaxSeanceMatin: 120,  // 2h
            debutApres: '15:00',
            finApres: '22:30',
            dureeMaxSeanceApres: 120   // 2h
        );

        return new HorairesOuverture(
            $horaireStandard, // Lundi
            $horaireStandard, // Mardi
            $horaireStandard, // Mercredi
            $horaireStandard, // Jeudi
            $horaireStandard, // Vendredi
            $horaireStandard, // Samedi
            $horaireStandard  // Dimanche
        );
    }

    private function createHorairesWeekend(): HorairesOuverture
    {
        // Fermé lundi, horaires standards mardi au dimanche
        $ferme           = HoraireJournalier::ferme();
        $horaireStandard = HoraireJournalier::create(
            debutMatin: '09:00',
            finMatin: '12:30',
            dureeMaxSeanceMatin: 120,  // 2h
            debutApres: '15:00',
            finApres: '22:30',
            dureeMaxSeanceApres: 120   // 2h
        );

        return new HorairesOuverture(
            $ferme,            // Lundi fermé
            $horaireStandard,  // Mardi
            $horaireStandard,  // Mercredi
            $horaireStandard,  // Jeudi
            $horaireStandard,  // Vendredi
            $horaireStandard,  // Samedi
            $horaireStandard   // Dimanche
        );
    }
}
