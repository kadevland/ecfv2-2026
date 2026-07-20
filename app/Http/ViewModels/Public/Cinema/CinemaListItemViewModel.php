<?php

declare(strict_types=1);

namespace App\Http\ViewModels\Public\Cinema;

use App\Application\Cinema\DTOs\CinemaListItemDto;

final class CinemaListItemViewModel
{
    // Property hooks PHP 8.4 - Accès direct aux données du DTO
    public string $cinema_id {
        get => $this->dto->uuid;
    }

    public string $nom {
        get => $this->dto->nom;
    }

    public string $adresse {
        get => $this->dto->adresse;
    }

    public string $ville {
        get => $this->dto->ville;
    }

    public string $code_postal {
        get => $this->dto->codePostal;
    }

    public ?string $telephone {
        get => $this->dto->telephone;
    }

    public ?string $email {
        get => $this->dto->email;
    }

    public int $nombre_salles {
        get => $this->dto->nombreSalles;
    }

    public bool $accessibilite_pmr {
        get => $this->dto->accessibilitePmr;
    }

    // Property hooks avec logique d'affichage
    /** @var array<int, string> */
    public array $services {
        get {
            $services = [];

            if ($this->dto->accessibilitePmr) {
                $services[] = 'Accessibilité PMR';
            }

            // Services basés sur le nombre de salles
            if ($this->dto->nombreSalles >= 6) {
                $services[] = '4K';
                $services[] = 'Dolby Atmos';
            }

            if ($this->dto->nombreSalles >= 8) {
                $services[] = 'IMAX';
            }

            return $services;
        }
    }

    /** @var array<string, mixed> */
    public array $horaires_ouverture {
        get {
            $horaires = $this->dto->horairesOuverture;

            $joursFr = [
                'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche',
            ];

            $horairesFormatted = [];
            foreach ($joursFr as $jour) {
                $horairesFormatted[$jour] = $horaires['aujourd_hui'] ?? '10:00-22:00';
            }

            return $horairesFormatted;
        }
    }

    // Méthodes utilitaires avec logique d'affichage plus complexe
    public string $adresse_complete {
        get => trim("{$this->dto->adresse}, {$this->dto->codePostal} {$this->dto->ville}");
    }

    public string $badge_capacite {
        get => match ($this->dto->nombreSalles) {
            0       => 'Aucune salle',
            1       => '1 salle',
            default => "{$this->dto->nombreSalles} salles"
        };
    }

    public function __construct(
        private CinemaListItemDto $dto
    ) {}
}
