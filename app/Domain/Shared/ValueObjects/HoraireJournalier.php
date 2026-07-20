<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use Carbon\CarbonImmutable;
use InvalidArgumentException;
use Respect\Validation\Validator as v;

/**
 * Value Object pour les horaires d'une journée
 * Gère matin et après-midi avec durées max pour séances
 */
final readonly class HoraireJournalier
{
    public function __construct(
        public readonly ?string $debutMatin,
        public readonly ?string $finMatin,
        public readonly ?int $dureeMaxSeanceMatin,
        public readonly ?string $debutApres,
        public readonly ?string $finApres,
        public readonly ?int $dureeMaxSeanceApres,
        private readonly bool $skipValidation = false
    ) {
        if (!$this->skipValidation) {
            $this->enforceInvariant();
        }
    }

    /**
     * Getter pour backward compatibility (accès depuis Blade)
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'finSeanceMatin' => $this->getHeureFinSeanceMatin(),
            'finSeanceApres' => $this->getHeureFinSeanceApres(),
            default          => throw new InvalidArgumentException("Property {$name} does not exist on HoraireJournalier")
        };
    }

    /**
     * Factory method pour créer depuis un formulaire
     */
    public static function create(
        ?string $debutMatin = null,
        ?string $finMatin = null,
        ?int $dureeMaxSeanceMatin = null,
        ?string $debutApres = null,
        ?string $finApres = null,
        ?int $dureeMaxSeanceApres = null
    ): self {
        return new self($debutMatin, $finMatin, $dureeMaxSeanceMatin, $debutApres, $finApres, $dureeMaxSeanceApres);
    }

    /**
     * Factory pour un jour fermé
     */
    public static function ferme(): self
    {
        return new self(null, null, null, null, null, null);
    }

    /**
     * Factory method pour créer sans validation (désérialisation DB)
     */
    public static function fromDbData(
        ?string $debutMatin = null,
        ?string $finMatin = null,
        ?int $dureeMaxSeanceMatin = null,
        ?string $debutApres = null,
        ?string $finApres = null,
        ?int $dureeMaxSeanceApres = null
    ): self {
        return new self(
            $debutMatin,
            $finMatin,
            $dureeMaxSeanceMatin,
            $debutApres,
            $finApres,
            $dureeMaxSeanceApres,
            skipValidation: true
        );
    }

    /**
     * Créer depuis un tableau (formulaire)
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        // Si pas ouvert ou pas de données, retourner fermé
        if (!isset($data['ouvert']) || !$data['ouvert']) {
            return self::ferme();
        }

        // Extraire les données en nettoyant les chaînes vides
        $debutMatin          = !empty($data['debut_matin']) ? $data['debut_matin'] : null;
        $finMatin            = !empty($data['fin_matin']) ? $data['fin_matin'] : null;
        $dureeMaxSeanceMatin = !empty($data['duree_max_seance_matin']) ? (int) $data['duree_max_seance_matin'] : null;
        $debutApres          = !empty($data['debut_apres']) ? $data['debut_apres'] : null;
        $finApres            = !empty($data['fin_apres']) ? $data['fin_apres'] : null;
        $dureeMaxSeanceApres = !empty($data['duree_max_seance_apres']) ? (int) $data['duree_max_seance_apres'] : null;

        // Vérifier la cohérence avant création
        $aMatinPartiel = ($debutMatin !== null) || ($finMatin !== null) || ($dureeMaxSeanceMatin !== null);
        $aMatinComplet = ($debutMatin !== null) && ($finMatin !== null) && ($dureeMaxSeanceMatin !== null);
        $aApresPartiel = ($debutApres !== null) || ($finApres !== null) || ($dureeMaxSeanceApres !== null);
        $aApresComplet = ($debutApres !== null) && ($finApres !== null) && ($dureeMaxSeanceApres !== null);

        // Si des champs matin sont remplis mais pas tous, c'est invalide
        if ($aMatinPartiel && !$aMatinComplet) {
            throw new InvalidArgumentException(
                'Si un créneau matin est défini, tous les champs matin sont obligatoires (debut_matin, fin_matin, duree_max_seance_matin)'
            );
        }

        // Si des champs après-midi sont remplis mais pas tous, c'est invalide
        if ($aApresPartiel && !$aApresComplet) {
            throw new InvalidArgumentException(
                'Si un créneau après-midi est défini, tous les champs après-midi sont obligatoires (debut_apres, fin_apres, duree_max_seance_apres)'
            );
        }

        // Si ouvert mais aucun créneau complet, c'est invalide
        if (!$aMatinComplet && !$aApresComplet) {
            throw new InvalidArgumentException(
                'Un horaire ouvert doit avoir au moins un créneau complet (matin ou après-midi)'
            );
        }

        return self::create($debutMatin, $finMatin, $dureeMaxSeanceMatin, $debutApres, $finApres, $dureeMaxSeanceApres);
    }

    /**
     * Obtenir les horaires du matin
     *
     * @return array{0: ?string, 1: ?string}
     */
    public function getHoraireMatin(): array
    {
        return [$this->debutMatin, $this->finMatin];
    }

    /**
     * Obtenir les horaires de l'après-midi
     *
     * @return array{0: string, 1: string}|null
     */
    public function getHoraireApres(): ?array
    {
        if ($this->debutApres === null || $this->finApres === null) {
            return null;
        }

        return [$this->debutApres, $this->finApres];
    }

    /**
     * Vérifier si la journée est complète (sans coupure)
     */
    public function journeeComplete(): bool
    {
        return $this->debutApres === null && $this->finApres === null;
    }

    /**
     * Vérifier si la journée est fermée
     */
    public function estFerme(): bool
    {
        return $this->debutMatin === null && $this->finMatin === null &&
               $this->debutApres === null && $this->finApres === null;
    }

    /**
     * Vérifier si la journée a au moins un créneau ouvert
     */
    public function estOuvert(): bool
    {
        return !$this->estFerme();
    }

    /**
     * Vérifier si une séance peut être programmée (planification interne)
     */
    public function peutProgrammerSeance(string $debut, int $dureeMinutes): bool
    {
        // Vérifier créneau matin
        if ($debut >= $this->debutMatin && $debut <= $this->finMatin) {
            $finSeance = $this->calculerFin($debut, $dureeMinutes);

            // La séance ne doit pas dépasser l'heure de fermeture ET respecter la durée max
            return $finSeance <= $this->finMatin && $dureeMinutes <= $this->dureeMaxSeanceMatin;
        }

        // Vérifier créneau après-midi si existe
        if ($this->debutApres !== null && $this->finApres !== null && $this->dureeMaxSeanceApres !== null) {
            if ($debut >= $this->debutApres && $debut <= $this->finApres) {
                $finSeance = $this->calculerFin($debut, $dureeMinutes);

                return $finSeance <= $this->finApres && $dureeMinutes <= $this->dureeMaxSeanceApres;
            }
        }

        return false;
    }

    /**
     * Vérifier si une séance est dans les horaires publics (affichage client)
     */
    public function seanceDansHorairesPublics(string $debut): bool
    {
        // Vérifier le créneau matin
        if ($debut >= $this->debutMatin && $debut <= $this->finMatin) {
            return true;
        }

        // Vérifier le créneau après-midi si existe
        if ($this->debutApres !== null && $this->finApres !== null) {
            return $debut >= $this->debutApres && $debut <= $this->finApres;
        }

        return false;
    }

    /**
     * Calculer l'heure limite pour les dernières séances
     */
    public function getHeureFinSeanceMatin(): string
    {
        return $this->calculerHeureFinSeance($this->finMatin, $this->dureeMaxSeanceMatin);
    }

    /**
     * Calculer l'heure limite pour les dernières séances après-midi
     */
    public function getHeureFinSeanceApres(): ?string
    {
        if ($this->finApres === null || $this->dureeMaxSeanceApres === null) {
            return null;
        }

        return $this->calculerHeureFinSeance($this->finApres, $this->dureeMaxSeanceApres);
    }

    /**
     * Convertir en tableau pour serialization
     *
     * @return array{debut_matin: ?string, fin_matin: ?string, duree_max_seance_matin: ?int, debut_apres: ?string, fin_apres: ?string, duree_max_seance_apres: ?int}
     */
    public function toArray(): array
    {
        return [
            'debut_matin'            => $this->debutMatin,
            'fin_matin'              => $this->finMatin,
            'duree_max_seance_matin' => $this->dureeMaxSeanceMatin,
            'debut_apres'            => $this->debutApres,
            'fin_apres'              => $this->finApres,
            'duree_max_seance_apres' => $this->dureeMaxSeanceApres,
        ];
    }

    /**
     * Obtenir un résumé textuel
     */
    public function getSummary(): string
    {
        if ($this->estFerme()) {
            return 'Fermé';
        }

        if ($this->journeeComplete()) {
            return $this->debutMatin . ' - ' . $this->finMatin;
        }

        return $this->debutMatin . '-' . $this->finMatin .
               ', ' . $this->debutApres . '-' . $this->finApres;
    }

    /**
     * Vérifier l'égalité avec un autre horaire
     */
    public function equals(HoraireJournalier $other): bool
    {
        return $this->debutMatin === $other->debutMatin &&
               $this->finMatin === $other->finMatin &&
               $this->dureeMaxSeanceMatin === $other->dureeMaxSeanceMatin &&
               $this->debutApres === $other->debutApres &&
               $this->finApres === $other->finApres &&
               $this->dureeMaxSeanceApres === $other->dureeMaxSeanceApres;
    }

    /**
     * Appliquer les invariants métier
     */
    private function enforceInvariant(): void
    {
        $this->validateTimeFormat();
        $this->validateTimeOrder();
        $this->validateCoherence();
        $this->validateDurees();
        $this->validateCreneaux();
    }

    /**
     * Valider le format des heures
     */
    private function validateTimeFormat(): void
    {
        // Valider format des heures définies
        $timesToValidate = array_filter([
            $this->debutMatin,
            $this->finMatin,
            $this->debutApres,
            $this->finApres,
        ], fn ($time) => $time !== null);

        foreach ($timesToValidate as $time) {
            if (!v::time('H:i')->validate($time)) {
                throw new InvalidArgumentException("Format d'heure invalide: {$time}");
            }
        }
    }

    /**
     * Valider l'ordre des heures
     */
    private function validateTimeOrder(): void
    {
        // Début matin < Fin matin (si les deux sont définis)
        if ($this->debutMatin !== null && $this->finMatin !== null) {
            if ($this->debutMatin >= $this->finMatin) {
                throw new InvalidArgumentException(
                    "L'heure de début matin doit être avant l'heure de fin matin"
                );
            }
        }

        // Si après-midi défini, vérifier l'ordre
        if ($this->debutApres !== null && $this->finApres !== null) {
            if ($this->debutApres >= $this->finApres) {
                throw new InvalidArgumentException(
                    "L'heure de début après-midi doit être avant l'heure de fin après-midi"
                );
            }

            // Fin matin <= Début après-midi
            if ($this->finMatin > $this->debutApres) {
                throw new InvalidArgumentException(
                    "La fin du matin ne peut pas être après le début de l'après-midi"
                );
            }
        }
    }

    /**
     * Valider la cohérence des données
     */
    private function validateCoherence(): void
    {
        // Si un des après-midi est défini, l'autre doit l'être aussi
        if (($this->debutApres !== null) !== ($this->finApres !== null)) {
            throw new InvalidArgumentException(
                'Début et fin après-midi doivent être tous les deux définis ou null'
            );
        }

        if (($this->debutApres !== null) !== ($this->dureeMaxSeanceApres !== null)) {
            throw new InvalidArgumentException(
                'Début et durée max séance après-midi doivent être cohérents'
            );
        }
    }

    /**
     * Valider les durées
     */
    private function validateDurees(): void
    {
        if ($this->dureeMaxSeanceMatin < 0) {
            throw new InvalidArgumentException(
                'La durée max des séances matin doit être positive'
            );
        }

        if ($this->dureeMaxSeanceApres !== null && $this->dureeMaxSeanceApres < 0) {
            throw new InvalidArgumentException(
                'La durée max des séances après-midi doit être positive'
            );
        }
    }

    /**
     * Calculer l'heure de fin à partir du début et de la durée
     */
    private function calculerFin(string $debut, int $dureeMinutes): string
    {
        return CarbonImmutable::createFromFormat('H:i', $debut)
            ->addMinutes($dureeMinutes)
            ->format('H:i');
    }

    /**
     * Calculer l'heure limite pour les séances depuis fermeture - durée max
     * (dernière heure de début de séance possible)
     */
    private function calculerHeureFinSeance(string $heureFermeture, int $dureeMaxMinutes): string
    {
        return CarbonImmutable::createFromFormat('H:i', $heureFermeture)
            ->subMinutes($dureeMaxMinutes)
            ->format('H:i');
    }

    /**
     * Valider qu'au moins un créneau est défini si l'objet existe
     */
    private function validateCreneaux(): void
    {
        $aMatinComplet = $this->debutMatin !== null && $this->finMatin !== null;
        $aApresComplet = $this->debutApres !== null && $this->finApres !== null;

        // Vérifier si c'est un horaire fermé (tous les champs null)
        $estFerme = $this->estFerme();

        // Si aucun créneau complet n'est défini ET que ce n'est pas fermé, c'est invalide
        if (!$aMatinComplet && !$aApresComplet && !$estFerme) {
            throw new InvalidArgumentException(
                'Au moins un créneau complet (matin ou après-midi) doit être défini pour un horaire ouvert'
            );
        }

        // Validation cohérence créneau matin
        if ($this->debutMatin !== null || $this->finMatin !== null || $this->dureeMaxSeanceMatin !== null) {
            if ($this->debutMatin === null || $this->finMatin === null || $this->dureeMaxSeanceMatin === null) {
                throw new InvalidArgumentException(
                    'Si un créneau matin est défini, debut_matin, fin_matin et duree_max_seance_matin sont obligatoires'
                );
            }
        }

        // Validation cohérence créneau après-midi
        if ($this->debutApres !== null || $this->finApres !== null) {
            if ($this->debutApres === null || $this->finApres === null) {
                throw new InvalidArgumentException(
                    'Si un créneau après-midi est défini, debut_apres et fin_apres sont obligatoires'
                );
            }
            // Si début et fin après-midi définis, la durée max est obligatoire
            if ($this->dureeMaxSeanceApres === null) {
                throw new InvalidArgumentException(
                    'Si un créneau après-midi est défini, duree_max_seance_apres est obligatoire'
                );
            }
        }
    }
}
