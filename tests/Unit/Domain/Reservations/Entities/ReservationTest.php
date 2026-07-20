<?php

declare(strict_types=1);

use Money\Money;
use Money\Currency;
use App\Domain\User\ValueObjects\UserId;
use App\Domain\Shared\ValueObjects\TauxTva;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Reservations\Entities\Reservation;
use App\Domain\Reservations\Events\ReservationCreated;
use App\Domain\Reservations\ValueObjects\ReservationId;
use App\Domain\Reservations\Events\ReservationCancelled;
use App\Domain\Reservations\Events\ReservationConfirmed;

describe('Reservation Entity', function () {

    function createValidReservation(): Reservation
    {
        return Reservation::creer(
            numeroReservation: 'RES-2025-001',
            userId: UserId::generate(),
            seanceId: SeanceId::generate(),
            nombrePlaces: 2,
            placesDetails: [
                'places' => [
                    ['rangee' => 'A', 'numero' => 1],
                    ['rangee' => 'A', 'numero' => 2],
                ],
            ],
            montantTotal: new Money(2400, new Currency('EUR')), // 24.00€
            montantHt: new Money(2000, new Currency('EUR')), // 20.00€
            tauxTva: TauxTva::create(20.0),
        );
    }

    describe('Création et Construction', function () {

        it('peut créer une réservation avec données valides', function () {
            $userId   = UserId::generate();
            $seanceId = SeanceId::generate();
            $tauxTva  = TauxTva::create(20.0);

            $reservation = Reservation::creer(
                numeroReservation: 'RES-2025-001',
                userId: $userId,
                seanceId: $seanceId,
                nombrePlaces: 3,
                placesDetails: [
                    'places' => [
                        ['rangee' => 'B', 'numero' => 5],
                        ['rangee' => 'B', 'numero' => 6],
                        ['rangee' => 'B', 'numero' => 7],
                    ],
                ],
                montantTotal: new Money(3600, new Currency('EUR')),
                montantHt: new Money(3000, new Currency('EUR')),
                tauxTva: $tauxTva
            );

            expect($reservation->id)->toBeInstanceOf(ReservationId::class);
            expect($reservation->numeroReservation)->toBe('RES-2025-001');
            expect($reservation->userId)->toBe($userId);
            expect($reservation->seanceId)->toBe($seanceId);
            expect($reservation->nombrePlaces)->toBe(3);
            expect($reservation->montantTotal->getAmount())->toBe(3600);
            expect($reservation->montantHt->getAmount())->toBe(3000);
            expect($reservation->tauxTva)->toBe($tauxTva);
            expect($reservation->statut)->toBe('en_attente_paiement');
            expect($reservation->qrCode)->toBeNull();
            expect($reservation->commentaires)->toBeNull();
        });

        it('génère un ID unique automatiquement', function () {
            $reservation1 = createValidReservation();
            $reservation2 = createValidReservation();

            expect($reservation1->id)->not->toBe($reservation2->id);
            expect($reservation1->id->value)->toBeString();
            expect($reservation2->id->value)->toBeString();
        });

        it('émet un événement ReservationCreated à la création', function () {
            $reservation = createValidReservation();

            $events = $reservation->getDomainEvents();
            expect($events)->toHaveCount(1);
            expect($events[0])->toBeInstanceOf(ReservationCreated::class);
        });

        it('peut créer avec date d\'expiration optionnelle', function () {
            $dateExpiration = new DateTime('+2 hours');

            $reservation = Reservation::creer(
                numeroReservation: 'RES-2025-002',
                userId: UserId::generate(),
                seanceId: SeanceId::generate(),
                nombrePlaces: 1,
                placesDetails: ['places' => [['rangee' => 'C', 'numero' => 10]]],
                montantTotal: new Money(1200, new Currency('EUR')),
                montantHt: new Money(1000, new Currency('EUR')),
                tauxTva: TauxTva::create(20.0),
                dateExpiration: $dateExpiration
            );

            expect($reservation->dateExpiration)->toBe($dateExpiration);
        });

        it('peut créer avec commentaires optionnels', function () {
            $commentaire = 'Réservation pour anniversaire';

            $reservation = Reservation::creer(
                numeroReservation: 'RES-2025-003',
                userId: UserId::generate(),
                seanceId: SeanceId::generate(),
                nombrePlaces: 4,
                placesDetails: ['places' => []],
                montantTotal: new Money(4800, new Currency('EUR')),
                montantHt: new Money(4000, new Currency('EUR')),
                tauxTva: TauxTva::create(20.0),
                commentaires: $commentaire
            );

            expect($reservation->commentaires)->toBe($commentaire);
        });
    });

    describe('Gestion du Statut', function () {

        it('démarre avec le statut en_attente_paiement', function () {
            $reservation = createValidReservation();

            expect($reservation->statut)->toBe('en_attente_paiement');
            expect($reservation->isConfirmed())->toBeFalse();
            expect($reservation->isCancelled())->toBeFalse();
        });

        it('peut être confirmée', function () {
            $reservation = createValidReservation();

            // Change le statut pour simuler un état confirmable
            $reservation = new Reservation(
                $reservation->id,
                $reservation->numeroReservation,
                $reservation->userId,
                $reservation->seanceId,
                $reservation->nombrePlaces,
                $reservation->placesDetails,
                $reservation->montantTotal,
                $reservation->montantHt,
                $reservation->tauxTva,
                'en_attente' // Statut confirmable
            );

            $reservation->confirmer();

            expect($reservation->statut)->toBe('confirmee');
            expect($reservation->isConfirmed())->toBeTrue();
            expect($reservation->isCancelled())->toBeFalse();
        });

        it('ne peut pas être confirmée si pas en attente', function () {
            $reservation = createValidReservation(); // statut = 'en_attente_paiement'

            expect(fn () => $reservation->confirmer())
                ->toThrow(DomainException::class, 'Seules les réservations en attente peuvent être confirmées');
        });

        it('peut être annulée', function () {
            $reservation = createValidReservation();
            $raison      = 'Changement de programme';

            $reservation->annuler($raison);

            expect($reservation->statut)->toBe('annulee');
            expect($reservation->isCancelled())->toBeTrue();
            expect($reservation->commentaires)->toBe($raison);
        });

        it('ne peut pas être annulée deux fois', function () {
            $reservation = createValidReservation();

            $reservation->annuler('Première annulation');

            expect(fn () => $reservation->annuler('Deuxième annulation'))
                ->toThrow(DomainException::class, 'La réservation est déjà annulée');
        });

        it('peut être marquée comme payée', function () {
            $reservation = createValidReservation(); // statut = 'en_attente_paiement'

            $reservation->markAsPaid();

            expect($reservation->statut)->toBe('payee');
        });

        it('ne peut pas être marquée comme payée si statut incorrect', function () {
            $reservation = createValidReservation();
            $reservation->annuler();

            expect(fn () => $reservation->markAsPaid())
                ->toThrow(DomainException::class);
        });
    });

    describe('Gestion des Dates', function () {

        it('détecte si la réservation est expirée', function () {
            $datePassee = new DateTime('-1 hour');

            $reservation = Reservation::creer(
                numeroReservation: 'RES-2025-004',
                userId: UserId::generate(),
                seanceId: SeanceId::generate(),
                nombrePlaces: 1,
                placesDetails: ['places' => []],
                montantTotal: new Money(1200, new Currency('EUR')),
                montantHt: new Money(1000, new Currency('EUR')),
                tauxTva: TauxTva::create(20.0),
                dateExpiration: $datePassee
            );

            expect($reservation->isExpired())->toBeTrue();
        });

        it('détecte si la réservation n\'est pas expirée', function () {
            $dateFuture = new DateTime('+2 hours');

            $reservation = Reservation::creer(
                numeroReservation: 'RES-2025-005',
                userId: UserId::generate(),
                seanceId: SeanceId::generate(),
                nombrePlaces: 1,
                placesDetails: ['places' => []],
                montantTotal: new Money(1200, new Currency('EUR')),
                montantHt: new Money(1000, new Currency('EUR')),
                tauxTva: TauxTva::create(20.0),
                dateExpiration: $dateFuture
            );

            expect($reservation->isExpired())->toBeFalse();
        });

        it('n\'est pas expirée sans date d\'expiration', function () {
            $reservation = createValidReservation(); // Pas de dateExpiration

            expect($reservation->isExpired())->toBeFalse();
        });

        it('peut changer la date d\'expiration', function () {
            $reservation  = createValidReservation();
            $nouvelleDate = new DateTime('+4 hours');

            $reservation->changerDateExpiration($nouvelleDate);

            expect($reservation->dateExpiration)->toBe($nouvelleDate);
        });
    });

    describe('Gestion QR Code', function () {

        it('génère un QR code', function () {
            $reservation = createValidReservation();

            $qrCode = $reservation->genererQrCode();

            expect($qrCode)->toBeString();
            expect($qrCode)->not->toBeEmpty();
            expect($reservation->qrCode)->toBe($qrCode);
        });

        it('retourne le même QR code si déjà généré', function () {
            $reservation = createValidReservation();

            $qrCode1 = $reservation->genererQrCode();
            $qrCode2 = $reservation->genererQrCode();

            expect($qrCode1)->toBe($qrCode2);
        });

        it('contient les données de réservation dans le QR code', function () {
            $reservation = createValidReservation();

            $qrCode      = $reservation->genererQrCode();
            $decodedData = json_decode(base64_decode($qrCode), true);

            expect($decodedData)->toHaveKey('reservation_id');
            expect($decodedData)->toHaveKey('numero');
            expect($decodedData)->toHaveKey('seance_id');
            expect($decodedData)->toHaveKey('user_id');
            expect($decodedData)->toHaveKey('places');
            expect($decodedData)->toHaveKey('timestamp');

            expect($decodedData['reservation_id'])->toBe($reservation->id->value);
            expect($decodedData['numero'])->toBe($reservation->numeroReservation);
            expect($decodedData['places'])->toBe($reservation->nombrePlaces);
        });
    });

    describe('Gestion des Places', function () {

        it('retourne les numéros de sièges formatés', function () {
            $reservation = Reservation::creer(
                numeroReservation: 'RES-2025-006',
                userId: UserId::generate(),
                seanceId: SeanceId::generate(),
                nombrePlaces: 3,
                placesDetails: [
                    'places' => [
                        ['rangee' => 'B', 'numero' => 5],
                        ['rangee' => 'B', 'numero' => 6],
                        ['rangee' => 'C', 'numero' => 1],
                    ],
                ],
                montantTotal: new Money(3600, new Currency('EUR')),
                montantHt: new Money(3000, new Currency('EUR')),
                tauxTva: TauxTva::create(20.0)
            );

            $seatNumbers = $reservation->getSeatNumbers();

            expect($seatNumbers)->toHaveCount(3);
            expect($seatNumbers)->toContain('B5');
            expect($seatNumbers)->toContain('B6');
            expect($seatNumbers)->toContain('C1');
        });

        it('retourne un tableau vide si pas de détails de places', function () {
            $reservation = Reservation::creer(
                numeroReservation: 'RES-2025-007',
                userId: UserId::generate(),
                seanceId: SeanceId::generate(),
                nombrePlaces: 2,
                placesDetails: [], // Pas de places définies
                montantTotal: new Money(2400, new Currency('EUR')),
                montantHt: new Money(2000, new Currency('EUR')),
                tauxTva: TauxTva::create(20.0)
            );

            $seatNumbers = $reservation->getSeatNumbers();

            expect($seatNumbers)->toBeArray()->toBeEmpty();
        });
    });

    describe('Calculs Financiers', function () {

        it('calcule le montant TTC correctement', function () {
            $reservation = createValidReservation();

            $montantTtc = $reservation->calculerMontantTtc();

            // 20.00€ HT + 20% TVA = 24.00€ TTC
            expect($montantTtc->getAmount())->toBe(2400);
            expect($montantTtc->getCurrency()->getCode())->toBe('EUR');
        });

        it('calcule avec différent taux de TVA', function () {
            $reservation = Reservation::creer(
                numeroReservation: 'RES-2025-008',
                userId: UserId::generate(),
                seanceId: SeanceId::generate(),
                nombrePlaces: 1,
                placesDetails: ['places' => []],
                montantTotal: new Money(1055, new Currency('EUR')),
                montantHt: new Money(1000, new Currency('EUR')), // 10.00€
                tauxTva: TauxTva::create(5.5) // TVA réduite
            );

            $montantTtc = $reservation->calculerMontantTtc();

            // 10.00€ HT + 5.5% TVA = 10.55€ TTC
            expect($montantTtc->getAmount())->toBe(1055);
        });
    });

    describe('Gestion des Commentaires', function () {

        it('peut ajouter un commentaire', function () {
            $reservation = createValidReservation();
            $commentaire = 'Siège côté allée souhaité';

            $reservation->ajouterCommentaire($commentaire);

            expect($reservation->commentaires)->toBe($commentaire);
        });

        it('peut modifier un commentaire existant', function () {
            $reservation = createValidReservation();

            $reservation->ajouterCommentaire('Premier commentaire');
            $reservation->ajouterCommentaire('Commentaire modifié');

            expect($reservation->commentaires)->toBe('Commentaire modifié');
        });
    });

    describe('Événements Domain', function () {

        it('émet ReservationConfirmed lors de la confirmation', function () {
            $reservation = new Reservation(
                ReservationId::generate(),
                'RES-TEST',
                UserId::generate(),
                SeanceId::generate(),
                1,
                ['places' => []],
                new Money(1200, new Currency('EUR')),
                new Money(1000, new Currency('EUR')),
                TauxTva::create(20.0),
                'en_attente'
            );

            $reservation->clearDomainEvents();
            $reservation->confirmer();

            $events = $reservation->getDomainEvents();
            expect($events)->toHaveCount(1);
            expect($events[0])->toBeInstanceOf(ReservationConfirmed::class);
        });

        it('émet ReservationCancelled lors de l\'annulation', function () {
            $reservation = createValidReservation();
            $reservation->clearDomainEvents();

            $reservation->annuler('Test annulation');

            $events = $reservation->getDomainEvents();
            expect($events)->toHaveCount(1);
            expect($events[0])->toBeInstanceOf(ReservationCancelled::class);
        });
    });
});
