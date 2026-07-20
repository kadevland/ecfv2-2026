<?php

declare(strict_types=1);

use App\Domain\Shared\Enums\CodePays;
use App\Domain\Cinema\Entities\Cinema;
use App\Domain\Shared\ValueObjects\Email;
use App\Domain\Shared\ValueObjects\Address;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Shared\ValueObjects\PhoneNumber;
use App\Domain\Shared\ValueObjects\CoordonneesGps;
use App\Domain\Shared\ValueObjects\HorairesOuverture;

uses(Tests\TestCase::class);

describe('Cinema Entity', function () {
    describe('Création et Construction', function () {
        it('peut créer un cinéma avec données minimales', function () {
            $adresse = Address::fromArray([
                'rue'         => '123 Rue du Cinema',
                'ville'       => 'Paris',
                'code_postal' => '75001',
                'pays'        => 'FR',
            ]);

            $cinema = Cinema::creer(
                'Cinéma Paradis',
                $adresse,
                CodePays::France
            );

            expect($cinema->id)->toBeInstanceOf(CinemaId::class);
            expect($cinema->nom)->toBe('Cinéma Paradis');
            expect($cinema->adresse)->toBe($adresse);
            expect($cinema->pays)->toBe(CodePays::France);
            expect($cinema->telephone)->toBeNull();
            expect($cinema->email)->toBeNull();
            expect($cinema->description)->toBeNull();
            expect($cinema->coordonneesGps)->toBeNull();
            expect($cinema->horairesOuverture)->toBeNull();
            expect($cinema->estActif)->toBeTrue();
        });

        it('peut créer un cinéma avec toutes les données', function () {
            $adresse = Address::fromArray([
                'rue'         => '123 Rue du Cinema',
                'ville'       => 'Paris',
                'code_postal' => '75001',
                'pays'        => 'FR',
                'complement'  => 'Batiment A',
            ]);

            $telephone   = PhoneNumber::fromE164('+33612345678');
            $email       = Email::fromString('contact@cinema.fr');
            $description = 'Un cinéma moderne avec tous les équipements.';

            $cinema = Cinema::creer(
                'Cinéma Complet',
                $adresse,
                CodePays::France,
                $telephone,
                $email,
                $description
            );

            expect($cinema->nom)->toBe('Cinéma Complet');
            expect($cinema->adresse)->toBe($adresse);
            expect($cinema->pays)->toBe(CodePays::France);
            expect($cinema->telephone)->toBe($telephone);
            expect($cinema->email)->toBe($email);
            expect($cinema->description)->toBe($description);
            expect($cinema->estActif)->toBeTrue();
        });

        it('peut créer avec constructeur direct et ID spécifique', function () {
            $id      = CinemaId::generate();
            $adresse = createTestAddress();

            $cinema = new Cinema(
                $id,
                'Cinéma Direct',
                $adresse,
                CodePays::Belgique,
                null,
                null,
                false // Inactif dès la création
            );

            expect($cinema->id)->toBe($id);
            expect($cinema->nom)->toBe('Cinéma Direct');
            expect($cinema->pays)->toBe(CodePays::Belgique);
            expect($cinema->estActif)->toBeFalse();
        });

        it('génère des IDs uniques automatiquement', function () {
            $cinema1 = createTestCinema();
            $cinema2 = createTestCinema();
            $cinema3 = createTestCinema();

            expect($cinema1->id->value)->not->toBe($cinema2->id->value);
            expect($cinema2->id->value)->not->toBe($cinema3->id->value);
            expect($cinema1->id->value)->not->toBe($cinema3->id->value);
        });

        it('supporte différents pays européens', function () {
            $adresse = createTestAddress();

            $cinemaFR = Cinema::creer('Cinéma FR', $adresse, CodePays::France);
            $cinemaBE = Cinema::creer('Cinéma BE', $adresse, CodePays::Belgique);
            $cinemaDE = Cinema::creer('Cinéma DE', $adresse, CodePays::Allemagne);
            $cinemaCH = Cinema::creer('Cinéma CH', $adresse, CodePays::Suisse);

            expect($cinemaFR->pays)->toBe(CodePays::France);
            expect($cinemaBE->pays)->toBe(CodePays::Belgique);
            expect($cinemaDE->pays)->toBe(CodePays::Allemagne);
            expect($cinemaCH->pays)->toBe(CodePays::Suisse);
        });
    });

    describe('Gestion du Nom', function () {
        it('peut changer le nom', function () {
            $cinema    = createTestCinema();
            $ancienNom = $cinema->nom;

            $cinema->changerNom('Nouveau Nom Cinéma');

            expect($cinema->nom)->toBe('Nouveau Nom Cinéma');
            expect($cinema->nom)->not->toBe($ancienNom);
        });

        it('peut changer le nom plusieurs fois', function () {
            $cinema = createTestCinema();

            $cinema->changerNom('Premier Changement');
            expect($cinema->nom)->toBe('Premier Changement');

            $cinema->changerNom('Deuxième Changement');
            expect($cinema->nom)->toBe('Deuxième Changement');

            $cinema->changerNom('Nom Final');
            expect($cinema->nom)->toBe('Nom Final');
        });

        it('peut changer le nom avec caractères spéciaux', function () {
            $cinema = createTestCinema();

            $nomAvecAccents = 'Cinéma Les Étoiles & Cie - 2024';
            $cinema->changerNom($nomAvecAccents);

            expect($cinema->nom)->toBe($nomAvecAccents);
        });
    });

    describe('Gestion de l\'Adresse', function () {
        it('peut changer l\'adresse et le pays', function () {
            $cinema          = createTestCinema();
            $ancienneAdresse = $cinema->adresse;
            $ancienPays      = $cinema->pays;

            $nouvelleAdresse = Address::fromArray([
                'rue'         => '456 Avenue Nouveau',
                'ville'       => 'Lyon',
                'code_postal' => '69000',
                'pays'        => 'FR',
            ]);

            $cinema->changerAdresse($nouvelleAdresse, CodePays::France);

            expect($cinema->adresse)->toBe($nouvelleAdresse);
            expect($cinema->adresse)->not->toBe($ancienneAdresse);
            expect($cinema->pays)->toBe(CodePays::France);
        });

        it('peut déménager dans un autre pays', function () {
            $cinema = createTestCinema(); // Créé en France

            $adresseBelge = Address::fromArray([
                'rue'         => 'Rue de la Loi 200',
                'ville'       => 'Bruxelles',
                'code_postal' => '1000',
                'pays'        => 'BE',
            ]);

            $cinema->changerAdresse($adresseBelge, CodePays::Belgique);

            expect($cinema->pays)->toBe(CodePays::Belgique);
            expect($cinema->adresse->ville)->toBe('Bruxelles');
        });

        it('fournit adresse complète formatée', function () {
            $cinema = createTestCinema();

            $adresseComplete = $cinema->getAdresseComplete();

            expect($adresseComplete)->toContain('123 Rue du Cinema');
            expect($adresseComplete)->toContain('Paris');
            expect($adresseComplete)->toContain('75001');
            expect($adresseComplete)->toContain('123 Rue du Cinema');
        });
    });

    describe('Gestion des Contacts', function () {
        it('peut changer téléphone et email ensemble', function () {
            $cinema = createTestCinema();

            $nouveauTelephone = PhoneNumber::fromE164('+33187654321');
            $nouvelEmail      = Email::fromString('nouveau@cinema.fr');

            $cinema->changerContact($nouveauTelephone, $nouvelEmail);

            expect($cinema->telephone)->toBe($nouveauTelephone);
            expect($cinema->email)->toBe($nouvelEmail);
        });

        it('peut ajouter contacts à un cinéma sans contacts', function () {
            $cinema = createTestCinema(); // Sans contacts
            expect($cinema->telephone)->toBeNull();
            expect($cinema->email)->toBeNull();

            $telephone = PhoneNumber::fromE164('+33123456789');
            $email     = Email::fromString('contact@cinema.fr');

            $cinema->changerContact($telephone, $email);

            expect($cinema->telephone)->toBe($telephone);
            expect($cinema->email)->toBe($email);
        });

        it('peut supprimer les contacts en passant null', function () {
            $cinema = Cinema::creer(
                'Cinéma Contact',
                createTestAddress(),
                CodePays::France,
                PhoneNumber::fromE164('+33123456789'),
                Email::fromString('test@cinema.fr')
            );

            expect($cinema->telephone)->not->toBeNull();
            expect($cinema->email)->not->toBeNull();

            $cinema->changerContact(null, null);

            expect($cinema->telephone)->toBeNull();
            expect($cinema->email)->toBeNull();
        });

        it('peut changer seulement le téléphone', function () {
            $cinema = createTestCinema();
            $email  = Email::fromString('keep@cinema.fr');
            $cinema->changerContact(null, $email);

            $nouveauTelephone = PhoneNumber::fromE164('+33987654321');
            $cinema->changerContact($nouveauTelephone, $email);

            expect($cinema->telephone)->toBe($nouveauTelephone);
            expect($cinema->email)->toBe($email);
        });

        it('peut changer seulement l\'email', function () {
            $cinema    = createTestCinema();
            $telephone = PhoneNumber::fromE164('+33123456789');
            $cinema->changerContact($telephone, null);

            $nouvelEmail = Email::fromString('newemail@cinema.fr');
            $cinema->changerContact($telephone, $nouvelEmail);

            expect($cinema->telephone)->toBe($telephone);
            expect($cinema->email)->toBe($nouvelEmail);
        });
    });

    describe('Gestion de la Description', function () {
        it('peut ajouter une description', function () {
            $cinema = createTestCinema();
            expect($cinema->description)->toBeNull(); // Pas de description par défaut

            $description = 'Un cinéma historique au cœur de Paris, spécialisé dans les films d\'auteur et les avant-premières.';
            $cinema->changerDescription($description);

            expect($cinema->description)->toBe($description);
        });

        it('peut modifier une description existante', function () {
            $cinema = createTestCinema();

            $premiereDescription = 'Description initiale du cinéma.';
            $cinema->changerDescription($premiereDescription);
            expect($cinema->description)->toBe($premiereDescription);

            $nouvelleDescription = 'Description mise à jour avec plus de détails sur notre programmation.';
            $cinema->changerDescription($nouvelleDescription);
            expect($cinema->description)->toBe($nouvelleDescription);
        });

        it('peut supprimer une description en passant null', function () {
            $cinema = createTestCinema();

            $cinema->changerDescription('Une description temporaire.');
            expect($cinema->description)->not->toBeNull();

            $cinema->changerDescription(null);
            expect($cinema->description)->toBeNull();
        });

        it('peut gérer des descriptions longues avec caractères spéciaux', function () {
            $cinema = createTestCinema();

            $descriptionLongue = 'Le Cinéma Paradis vous accueille depuis 1952 dans un cadre authentique & chaleureux. ' .
                                'Nous proposons une programmation éclectique : films d\'auteur, blockbusters, documentaires, ' .
                                'séances spéciales pour enfants... Plus de 70 ans d\'histoire & de passion du cinéma ! ' .
                                'Venez découvrir notre salle historique de 150 places avec écran 4K & son Dolby Atmos.';

            $cinema->changerDescription($descriptionLongue);
            expect($cinema->description)->toBe($descriptionLongue);
        });

        it('peut créer un cinéma avec description dès la création', function () {
            $adresse     = createTestAddress();
            $description = 'Cinéma moderne avec équipements dernière génération.';

            $cinema = Cinema::creer(
                'Cinéma Moderne',
                $adresse,
                CodePays::France,
                null,
                null,
                $description
            );

            expect($cinema->description)->toBe($description);
        });
    });

    describe('Gestion des Coordonnées GPS', function () {
        it('peut ajouter des coordonnées GPS', function () {
            $cinema = createTestCinema();
            expect($cinema->coordonneesGps)->toBeNull();

            $coordonnees = CoordonneesGps::creer(48.8566, 2.3522);
            $cinema->changerCoordonneesGps($coordonnees);

            expect($cinema->coordonneesGps)->toBe($coordonnees);
            expect($cinema->getGoogleMapsUrl())->toContain('https://www.google.com/maps');
        });

        it('peut calculer distance entre cinémas', function () {
            $cinema1 = createTestCinema();
            $cinema2 = createTestCinema();

            $coordonnees1 = CoordonneesGps::creer(48.8566, 2.3522); // Paris
            $coordonnees2 = CoordonneesGps::creer(50.8503, 4.3517); // Bruxelles

            $cinema1->changerCoordonneesGps($coordonnees1);
            $cinema2->changerCoordonneesGps($coordonnees2);

            $distance = $cinema1->getDistanceVers($cinema2);
            expect($distance)->toBeGreaterThan(0);
        });

        it('retourne null si coordonnées manquantes', function () {
            $cinema1 = createTestCinema();
            $cinema2 = createTestCinema();

            expect($cinema1->getDistanceVers($cinema2))->toBeNull();
            expect($cinema1->getGoogleMapsUrl())->toBeNull();
        });
    });

    describe('Gestion des Horaires d\'Ouverture', function () {
        it('peut définir des horaires d\'ouverture', function () {
            $cinema = createTestCinema();
            expect($cinema->horairesOuverture)->toBeNull();

            $horaires = HorairesOuverture::fromArray([
                'lundi'    => ['09:00', '22:00'],
                'mardi'    => ['09:00', '22:00'],
                'mercredi' => ['09:00', '22:00'],
                'jeudi'    => ['09:00', '22:00'],
                'vendredi' => ['09:00', '23:00'],
                'samedi'   => ['09:00', '23:00'],
                'dimanche' => ['14:00', '22:00'],
            ]);
            $cinema->changerHorairesOuverture($horaires);

            expect($cinema->horairesOuverture)->toBe($horaires);
        });

        it('peut supprimer les horaires', function () {
            $cinema   = createTestCinema();
            $horaires = HorairesOuverture::fromArray([
                'lundi' => ['09:00', '22:00'],
                'mardi' => ['09:00', '22:00'],
            ]);
            $cinema->changerHorairesOuverture($horaires);

            expect($cinema->horairesOuverture)->not->toBeNull();

            $cinema->changerHorairesOuverture(null);
            expect($cinema->horairesOuverture)->toBeNull();
        });
    });

    describe('Gestion de l\'État Actif/Inactif', function () {
        it('est actif par défaut à la création', function () {
            $cinema = createTestCinema();
            expect($cinema->estActif)->toBeTrue();
        });

        it('peut être désactivé', function () {
            $cinema = createTestCinema();

            $cinema->desactiver();

            expect($cinema->estActif)->toBeFalse();
        });

        it('peut être réactivé après désactivation', function () {
            $cinema = createTestCinema();
            $cinema->desactiver();
            expect($cinema->estActif)->toBeFalse();

            $cinema->activer();

            expect($cinema->estActif)->toBeTrue();
        });

        it('peut être activé/désactivé plusieurs fois', function () {
            $cinema = createTestCinema();

            // Cycle 1
            $cinema->desactiver();
            expect($cinema->estActif)->toBeFalse();
            $cinema->activer();
            expect($cinema->estActif)->toBeTrue();

            // Cycle 2
            $cinema->desactiver();
            expect($cinema->estActif)->toBeFalse();
            $cinema->activer();
            expect($cinema->estActif)->toBeTrue();

            // Cycle 3
            $cinema->desactiver();
            expect($cinema->estActif)->toBeFalse();
        });

        it('activer un cinéma déjà actif ne change rien', function () {
            $cinema = createTestCinema();
            expect($cinema->estActif)->toBeTrue();

            $cinema->activer(); // Redondant

            expect($cinema->estActif)->toBeTrue();
        });

        it('désactiver un cinéma déjà inactif ne change rien', function () {
            $cinema = createTestCinema();
            $cinema->desactiver();
            expect($cinema->estActif)->toBeFalse();

            $cinema->desactiver(); // Redondant

            expect($cinema->estActif)->toBeFalse();
        });

        it('peut être créé inactif via constructeur', function () {
            $cinema = new Cinema(
                CinemaId::generate(),
                'Cinéma Inactif',
                createTestAddress(),
                CodePays::France,
                null,
                null,
                false // estActif = false
            );

            expect($cinema->estActif)->toBeFalse();
        });
    });

    describe('Intégration et Scénarios Complexes', function () {
        it('peut subir tous les changements en séquence', function () {
            $cinema = createTestCinema();

            // Changement nom
            $cinema->changerNom('Cinéma Évolution');

            // Changement adresse
            $nouvelleAdresse = Address::fromArray([
                'rue'         => '789 Boulevard Nouveau',
                'ville'       => 'Marseille',
                'code_postal' => '13000',
                'pays'        => 'FR',
            ]);
            $cinema->changerAdresse($nouvelleAdresse, CodePays::France);

            // Ajout contacts
            $telephone = PhoneNumber::fromE164('+33491234567');
            $email     = Email::fromString('marseille@cinema.fr');
            $cinema->changerContact($telephone, $email);

            // Ajout description et coordonnées
            $cinema->changerDescription('Cinéma moderne à Marseille');
            $coordonnees = CoordonneesGps::creer(43.2965, 5.3698);
            $cinema->changerCoordonneesGps($coordonnees);

            // Désactivation temporaire
            $cinema->desactiver();

            // Vérifications finales
            expect($cinema->nom)->toBe('Cinéma Évolution');
            expect($cinema->adresse->ville)->toBe('Marseille');
            expect($cinema->telephone->telephoneE164)->toBe('+33491234567');
            expect($cinema->email->toString())->toBe('marseille@cinema.fr');
            expect($cinema->description)->toBe('Cinéma moderne à Marseille');
            expect($cinema->coordonneesGps)->toBe($coordonnees);
            expect($cinema->estActif)->toBeFalse();

            // Réactivation
            $cinema->activer();
            expect($cinema->estActif)->toBeTrue();
        });

        it('conserve l\'identité à travers tous les changements', function () {
            $cinema     = createTestCinema();
            $idOriginal = $cinema->id;

            // Nombreux changements
            $cinema->changerNom('Nouveau Nom');
            $cinema->changerAdresse(createTestAddress(), CodePays::Belgique);
            $cinema->changerContact(PhoneNumber::fromE164('+32123456789'), Email::fromString('be@cinema.be'));
            $cinema->changerDescription('Cinéma belge moderne');
            $cinema->desactiver();
            $cinema->activer();

            // L'ID ne change jamais
            expect($cinema->id)->toBe($idOriginal);
            expect($cinema->id->value)->toBe($idOriginal->value);
        });

        it('peut créer cinémas pour tous les pays supportés', function () {
            $adresse    = createTestAddress();
            $paysEtNoms = [
                ['pays' => CodePays::France, 'nom' => 'Cinéma Français'],
                ['pays' => CodePays::Belgique, 'nom' => 'Cinéma Belge'],
                ['pays' => CodePays::Allemagne, 'nom' => 'Kino Deutsch'],
                ['pays' => CodePays::Suisse, 'nom' => 'Cinéma Suisse'],
                ['pays' => CodePays::Italie, 'nom' => 'Cinema Italiano'],
                ['pays' => CodePays::Espagne, 'nom' => 'Cine Español'],
            ];

            foreach ($paysEtNoms as $data) {
                $cinema = Cinema::creer($data['nom'], $adresse, $data['pays']);

                expect($cinema->nom)->toBe($data['nom']);
                expect($cinema->pays)->toBe($data['pays']);
                expect($cinema->estActif)->toBeTrue();
            }
        });
    });

    describe('Value Objects et Immutabilité', function () {
        it('les Value Objects ne sont pas modifiés par référence', function () {
            $adresseOriginale = createTestAddress();
            $cinema           = Cinema::creer('Test', $adresseOriginale, CodePays::France);

            // L'adresse du cinéma est celle passée
            expect($cinema->adresse)->toBe($adresseOriginale);

            // Changer l'adresse du cinéma ne modifie pas l'originale
            $nouvelleAdresse = Address::fromArray([
                'rue'         => 'Autre rue',
                'ville'       => 'Autre ville',
                'code_postal' => '1000',
                'pays'        => 'BE',
            ]);
            $cinema->changerAdresse($nouvelleAdresse, CodePays::Belgique);

            // L'adresse originale n'est pas modifiée
            expect($adresseOriginale->rue)->toBe('123 Rue du Cinema');
            expect($cinema->adresse->rue)->toBe('Autre rue');
        });

        it('les propriétés respectent l\'encapsulation métier', function () {
            $cinema = createTestCinema();

            // Toutes les propriétés sont accessibles en lecture publique
            expect($cinema->id)->toBeInstanceOf(CinemaId::class);
            expect($cinema->nom)->toBeString();
            expect($cinema->adresse)->toBeInstanceOf(Address::class);
            expect($cinema->pays)->toBeInstanceOf(CodePays::class);
            expect($cinema->estActif)->toBeBool();
            expect($cinema->telephone)->toBeNull(); // Par défaut
            expect($cinema->email)->toBeNull(); // Par défaut
            expect($cinema->coordonneesGps)->toBeNull(); // Par défaut
            expect($cinema->horairesOuverture)->toBeNull(); // Par défaut
            expect($cinema->description)->toBeNull(); // Par défaut

            // Les propriétés sont immuables - modifications via méthodes métier uniquement
            $idOriginal  = $cinema->id;
            $nomOriginal = $cinema->nom;

            // L'ID ne peut jamais changer (readonly)
            expect($cinema->id)->toBe($idOriginal);

            // Les autres propriétés changent via méthodes métier
            $cinema->changerNom('Nouveau Nom');
            expect($cinema->nom)->toBe('Nouveau Nom');
            expect($cinema->nom)->not->toBe($nomOriginal);

            $cinema->desactiver();
            expect($cinema->estActif)->toBeFalse();

            $cinema->activer();
            expect($cinema->estActif)->toBeTrue();

            // Après tous les changements, l'ID reste identique
            expect($cinema->id)->toBe($idOriginal);
        });
    });
});
