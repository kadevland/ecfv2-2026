<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Cinema\Entities;

use DateTime;
use Tests\TestCase;
use DateTimeInterface;
use App\Domain\Cinema\Entities\Film;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Cinema\ValueObjects\FilmId;

final class FilmTest extends TestCase
{
    #[Test]
    public function it_creates_film_successfully(): void
    {
        // When
        $film = Film::create(
            titre: 'Avatar 3',
            realisateurs: ['James Cameron'],
            genres: ['Science-fiction', 'Action'],
            dureeMinutes: 180,
            classification: 'PG-13',
            dateSortie: new DateTime('2025-12-25'),
            acteursPrincipaux: ['Sam Worthington', 'Zoe Saldana'],
            langueOriginale: 'en',
            resume: 'Suite épique de la saga Avatar',
            afficheUrl: 'avatar3.jpg'
        );

        // Then
        $this->assertInstanceOf(Film::class, $film);
        $this->assertInstanceOf(FilmId::class, $film->id);
        $this->assertEquals('Avatar 3', $film->titre);
        $this->assertEquals(['James Cameron'], $film->realisateurs);
        $this->assertEquals(['Sam Worthington', 'Zoe Saldana'], $film->acteursPrincipaux);
        $this->assertEquals(['Science-fiction', 'Action'], $film->genres);
        $this->assertEquals(180, $film->dureeMinutes);
        $this->assertEquals('PG-13', $film->classification);
        $this->assertEquals('en', $film->langueOriginale);
        $this->assertEquals('Suite épique de la saga Avatar', $film->synopsis);
        $this->assertEquals('2025-12-25', $film->dateSortie->format('Y-m-d'));
        $this->assertEquals('avatar3.jpg', $film->afficheUrl);
        $this->assertTrue($film->estActif);
        $this->assertNull($film->noteCritique);
        $this->assertNull($film->notePublic);
    }

    #[Test]
    public function it_creates_film_with_optional_parameters(): void
    {
        // When
        $film = Film::create(
            titre: 'Film Test',
            realisateurs: ['Director Test'],
            genres: ['Drama'],
            dureeMinutes: 120,
            classification: 'PG',
            dateSortie: new DateTime('2025-06-15'),
            titreFr: 'Film Test FR',
            acteursPrincipaux: ['Actor Test'],
            langueOriginale: 'fr',
            sousTitres: 'en', // String, not array
            resume: 'Test synopsis',
            dateFinExploitation: new DateTime('2025-12-15'),
            notePresse: 8.5,
            notePublic: 9.0,
            afficheUrl: 'test.jpg',
            bandeAnnonceUrl: 'test-trailer.mp4',
            estActif: false
        );

        // Then
        $this->assertEquals('Film Test FR', $film->titreOriginal);
        $this->assertEquals(['en'], $film->sousTitres); // Converted to array
        $this->assertEquals('2025-12-15', $film->dateFinExploitation->format('Y-m-d'));
        $this->assertEquals(8.5, $film->noteCritique);
        $this->assertEquals(9.0, $film->notePublic);
        $this->assertEquals('test-trailer.mp4', $film->bandeAnnonceUrl);
        $this->assertFalse($film->estActif);
    }

    #[Test]
    public function it_changes_titre_successfully(): void
    {
        // Given
        $film     = $this->createSampleFilm();
        $oldTitre = $film->titre;

        // When
        $film->changerTitre('Nouveau Titre');

        // Then
        $this->assertEquals('Nouveau Titre', $film->titre);
        $this->assertNotEquals($oldTitre, $film->titre);
    }

    #[Test]
    public function it_changes_realisateurs_successfully(): void
    {
        // Given
        $film = $this->createSampleFilm();

        // When
        $film->changerRealisateurs(['Christopher Nolan', 'Denis Villeneuve']);

        // Then
        $this->assertEquals(['Christopher Nolan', 'Denis Villeneuve'], $film->realisateurs);
    }

    #[Test]
    public function it_gets_primary_director(): void
    {
        // Given
        $film = $this->createSampleFilm();

        // When
        $primaryDirector = $film->getPrimaryDirector();

        // Then
        $this->assertEquals('Test Director', $primaryDirector);
    }

    #[Test]
    public function it_changes_genres_successfully(): void
    {
        // Given
        $film = $this->createSampleFilm();

        // When
        $film->changerGenres(['Thriller', 'Mystery']);

        // Then
        $this->assertEquals(['Thriller', 'Mystery'], $film->genres);
    }

    #[Test]
    public function it_changes_duree_successfully(): void
    {
        // Given
        $film = $this->createSampleFilm();

        // When
        $film->changerDuree(150);

        // Then
        $this->assertEquals(150, $film->dureeMinutes);
    }

    #[Test]
    public function it_changes_classification_successfully(): void
    {
        // Given
        $film = $this->createSampleFilm();

        // When
        $film->changerClassification('R');

        // Then
        $this->assertEquals('R', $film->classification);
    }

    #[Test]
    public function it_changes_date_sortie_successfully(): void
    {
        // Given
        $film    = $this->createSampleFilm();
        $newDate = new DateTime('2026-01-15');

        // When
        $film->changerDateSortie($newDate);

        // Then
        $this->assertEquals('2026-01-15', $film->dateSortie->format('Y-m-d'));
    }

    #[Test]
    public function it_changes_synopsis_successfully(): void
    {
        // Given
        $film = $this->createSampleFilm();

        // When
        $film->changerSynopsis('Nouveau résumé du film');

        // Then
        $this->assertEquals('Nouveau résumé du film', $film->synopsis);
    }

    #[Test]
    public function it_changes_affiche_successfully(): void
    {
        // Given
        $film = $this->createSampleFilm();

        // When
        $film->changerAffiche('nouvelle-affiche.jpg');

        // Then
        $this->assertEquals('nouvelle-affiche.jpg', $film->afficheUrl);
    }

    #[Test]
    public function it_activates_film(): void
    {
        // Given
        $film = $this->createSampleFilm();
        $film->desactiver();

        // When
        $film->activer();

        // Then
        $this->assertTrue($film->estActif);
    }

    #[Test]
    public function it_deactivates_film(): void
    {
        // Given
        $film = $this->createSampleFilm();

        // When
        $film->desactiver();

        // Then
        $this->assertFalse($film->estActif);
    }

    #[Test]
    public function it_adds_avis_and_calculates_average(): void
    {
        // Given
        $film = $this->createSampleFilm();

        // When
        $film->ajouterAvis(8.0);
        $film->ajouterAvis(9.0);
        $film->ajouterAvis(7.0);

        // Then
        $this->assertEquals(8.0, $film->noteMoyenneAvis);
        $this->assertEquals(3, $film->nombreAvis);
    }

    #[Test]
    public function it_gets_formatted_duration(): void
    {
        // Given
        $film = $this->createSampleFilm(); // 120 minutes

        // When
        $formattedDuration = $film->getFormattedDuration();

        // Then
        $this->assertEquals('2h', $formattedDuration);
    }

    #[Test]
    public function it_checks_if_film_is_in_theaters(): void
    {
        // Given
        $filmInTheaters = Film::create(
            titre: 'Film En Cours',
            realisateurs: ['Director'],
            genres: ['Drama'],
            dureeMinutes: 120,
            classification: 'PG',
            dateSortie: new DateTime('-1 month')
        );

        $filmNotYetReleased = Film::create(
            titre: 'Film Futur',
            realisateurs: ['Director'],
            genres: ['Drama'],
            dureeMinutes: 120,
            classification: 'PG',
            dateSortie: new DateTime('+1 month')
        );

        // Then
        $this->assertTrue($filmInTheaters->isInTheaters());
        $this->assertFalse($filmNotYetReleased->isInTheaters());
    }

    #[Test]
    public function it_terminates_exploitation(): void
    {
        // Given
        $film = $this->createSampleFilm();

        // When
        $film->terminerExploitation();

        // Then
        $this->assertFalse($film->estActif);
        $this->assertInstanceOf(DateTimeInterface::class, $film->dateFinExploitation);
    }

    #[Test]
    public function it_changes_bande_annonce(): void
    {
        // Given
        $film = $this->createSampleFilm();

        // When
        $film->changerBandeAnnonce('nouvelle-bande-annonce.mp4');

        // Then
        $this->assertEquals('nouvelle-bande-annonce.mp4', $film->bandeAnnonceUrl);
    }

    private function createSampleFilm(): Film
    {
        return Film::create(
            titre: 'Film Test',
            realisateurs: ['Test Director'],
            genres: ['Drama'],
            dureeMinutes: 120,
            classification: 'PG',
            dateSortie: new DateTime('2025-06-15'),
            acteursPrincipaux: ['Test Actor'],
            langueOriginale: 'fr',
            resume: 'Test synopsis',
            afficheUrl: 'test.jpg'
        );
    }
}
