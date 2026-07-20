<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use Exception;
use InvalidArgumentException;
use App\Domain\Shared\Enums\JourSemaine;

/**
 * Value Object pour les horaires d'ouverture d'un cinéma (7 jours)
 * Contient 7 HoraireJournalier
 */
final readonly class HorairesOuverture
{
    public function __construct(
        public readonly HoraireJournalier $lundi,
        public readonly HoraireJournalier $mardi,
        public readonly HoraireJournalier $mercredi,
        public readonly HoraireJournalier $jeudi,
        public readonly HoraireJournalier $vendredi,
        public readonly HoraireJournalier $samedi,
        public readonly HoraireJournalier $dimanche,
        private readonly bool $skipValidation = false
    ) {
        if (!$this->skipValidation) {
            $this->enforceInvariant();
        }
    }

    /**
     * Créer depuis un tableau de données
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            self::getHoraireFromData($data, JourSemaine::LUNDI),
            self::getHoraireFromData($data, JourSemaine::MARDI),
            self::getHoraireFromData($data, JourSemaine::MERCREDI),
            self::getHoraireFromData($data, JourSemaine::JEUDI),
            self::getHoraireFromData($data, JourSemaine::VENDREDI),
            self::getHoraireFromData($data, JourSemaine::SAMEDI),
            self::getHoraireFromData($data, JourSemaine::DIMANCHE)
        );
    }

    /**
     * @param array<string, mixed>|null $data
     */
    public static function tryFromArray(?array $data): ?self
    {
        if ($data === null || empty($data)) {
            return null;
        }

        try {
            return self::fromArray($data);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * Créer depuis JSON
     */
    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return self::fromArray($data ?? []);
    }

    /**
     * Créer horaires par défaut standard (9h-12h30/15h-22h30 +2h)
     */
    public static function standard(): self
    {
        $horaireStandard = new HoraireJournalier(
            debutMatin: '09:00',
            finMatin: '12:30',
            dureeMaxSeanceMatin: 120,  // 2h
            debutApres: '15:00',
            finApres: '22:30',
            dureeMaxSeanceApres: 120   // 2h
        );

        return new self(
            lundi: $horaireStandard,
            mardi: $horaireStandard,
            mercredi: $horaireStandard,
            jeudi: $horaireStandard,
            vendredi: $horaireStandard,
            samedi: $horaireStandard,
            dimanche: $horaireStandard
        );
    }

    /**
     * Créer depuis des données de base de données (sans validation stricte)
     *
     * @param array<string, mixed> $data
     */
    public static function fromDbData(array $data): self
    {
        return new self(
            self::getHoraireFromDbData($data, JourSemaine::LUNDI),
            self::getHoraireFromDbData($data, JourSemaine::MARDI),
            self::getHoraireFromDbData($data, JourSemaine::MERCREDI),
            self::getHoraireFromDbData($data, JourSemaine::JEUDI),
            self::getHoraireFromDbData($data, JourSemaine::VENDREDI),
            self::getHoraireFromDbData($data, JourSemaine::SAMEDI),
            self::getHoraireFromDbData($data, JourSemaine::DIMANCHE)
        );
    }

    /**
     * Obtenir l'horaire pour un jour donné
     */
    public function getHoraireJour(JourSemaine $jour): HoraireJournalier
    {
        return match ($jour) {
            JourSemaine::LUNDI    => $this->lundi,
            JourSemaine::MARDI    => $this->mardi,
            JourSemaine::MERCREDI => $this->mercredi,
            JourSemaine::JEUDI    => $this->jeudi,
            JourSemaine::VENDREDI => $this->vendredi,
            JourSemaine::SAMEDI   => $this->samedi,
            JourSemaine::DIMANCHE => $this->dimanche,
        };
    }

    /**
     * Obtenir l'horaire pour un jour donné de manière sécurisée
     * Retourne un horaire fermé en cas d'erreur
     */
    public function getHoraireJourSafe(JourSemaine $jour): HoraireJournalier
    {
        try {
            return $this->getHoraireJour($jour);
        } catch (Exception) {
            return HoraireJournalier::ferme();
        }
    }

    /**
     * Vérifier si le cinéma est ouvert un jour donné
     */
    public function isOpenOn(JourSemaine $jour): bool
    {
        return !$this->getHoraireJour($jour)
            ->estFerme();
    }

    /**
     * Vérifier si une séance peut être programmée
     */
    public function peutProgrammerSeance(JourSemaine $jour, string $heureDebut, int $dureeMinutes): bool
    {
        if (!$this->isOpenOn($jour)) {
            return false;
        }

        return $this->getHoraireJour($jour)
            ->peutProgrammerSeance($heureDebut, $dureeMinutes);
    }

    /**
     * Vérifier si une séance est dans les horaires publics
     */
    public function seanceDansHorairesPublics(JourSemaine $jour, string $heureDebut): bool
    {
        if (!$this->isOpenOn($jour)) {
            return false;
        }

        return $this->getHoraireJour($jour)
            ->seanceDansHorairesPublics($heureDebut);
    }

    /**
     * Obtenir tous les jours ouverts
     *
     * @return array<JourSemaine>
     */
    public function getJoursOuverts(): array
    {
        $joursOuverts = [];

        foreach (JourSemaine::cases() as $jour) {
            if ($this->isOpenOn($jour)) {
                $joursOuverts[] = $jour;
            }
        }

        return $joursOuverts;
    }

    /**
     * Obtenir tous les jours ouverts de manière sécurisée
     * Retourne un tableau vide en cas d'erreur
     *
     * @return array<JourSemaine>
     */
    public function getJoursOuvertsSafe(): array
    {
        try {
            return $this->getJoursOuverts();
        } catch (Exception) {
            return [];
        }
    }

    /**
     * Convertir en tableau pour serialization
     *
     * @return array<string, array<string, mixed>>
     */
    public function toArray(): array
    {
        return [
            JourSemaine::LUNDI->value    => $this->lundi->toArray(),
            JourSemaine::MARDI->value    => $this->mardi->toArray(),
            JourSemaine::MERCREDI->value => $this->mercredi->toArray(),
            JourSemaine::JEUDI->value    => $this->jeudi->toArray(),
            JourSemaine::VENDREDI->value => $this->vendredi->toArray(),
            JourSemaine::SAMEDI->value   => $this->samedi->toArray(),
            JourSemaine::DIMANCHE->value => $this->dimanche->toArray(),
        ];
    }

    /**
     * Convertir en JSON pour base de données
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * Obtenir un résumé textuel des horaires
     */
    public function getSummary(): string
    {
        $summary = [];

        foreach (JourSemaine::cases() as $jour) {
            $horaire   = $this->getHoraireJour($jour);
            $summary[] = $jour->getLabel() . ': ' . $horaire->getSummary();
        }

        return implode(' | ', $summary);
    }

    /**
     * Vérifier l'égalité avec un autre objet
     */
    public function equals(HorairesOuverture $other): bool
    {
        return $this->lundi->equals($other->lundi) &&
            $this->mardi->equals($other->mardi) &&
            $this->mercredi->equals($other->mercredi) &&
            $this->jeudi->equals($other->jeudi) &&
            $this->vendredi->equals($other->vendredi) &&
            $this->samedi->equals($other->samedi) &&
            $this->dimanche->equals($other->dimanche);
    }

    /**
     * Extraire un horaire depuis les données d'un jour
     *
     * @param array<string, mixed> $data
     */
    private static function getHoraireFromData(array $data, JourSemaine $jour): HoraireJournalier
    {
        $jourData = $data[$jour->value] ?? null;

        // Si pas de données pour ce jour, retourner fermé
        if ($jourData === null || !is_array($jourData)) {
            return HoraireJournalier::ferme();
        }

        return HoraireJournalier::fromArray($jourData);
    }

    /**
     * Extraire un horaire depuis les données DB d'un jour (sans validation stricte)
     *
     * @param array<string, mixed> $data
     */
    private static function getHoraireFromDbData(array $data, JourSemaine $jour): HoraireJournalier
    {
        $jourData = $data[$jour->value] ?? null;

        // Si pas de données pour ce jour, retourner fermé
        if ($jourData === null || !is_array($jourData)) {
            return HoraireJournalier::ferme();
        }

        // Utiliser fromDbData pour éviter la validation stricte
        return HoraireJournalier::fromDbData(
            debutMatin: !empty($jourData['debut_matin']) ? $jourData['debut_matin'] : null,
            finMatin: !empty($jourData['fin_matin']) ? $jourData['fin_matin'] : null,
            dureeMaxSeanceMatin: !empty($jourData['duree_max_seance_matin']) ? (int) $jourData['duree_max_seance_matin'] : null,
            debutApres: !empty($jourData['debut_apres']) ? $jourData['debut_apres'] : null,
            finApres: !empty($jourData['fin_apres']) ? $jourData['fin_apres'] : null,
            dureeMaxSeanceApres: !empty($jourData['duree_max_seance_apres']) ? (int) $jourData['duree_max_seance_apres'] : null
        );
    }

    /**
     * Appliquer les invariants métier
     */
    private function enforceInvariant(): void
    {
        // Les invariants sont déjà appliqués par chaque HoraireJournalier
        // Ici on peut ajouter des règles métier globales si nécessaire

        // Exemple: Au moins un jour doit être ouvert
        if (!$this->hasAtLeastOneOpenDay()) {
            throw new InvalidArgumentException('Un cinéma doit être ouvert au moins un jour par semaine');
        }
    }

    /**
     * Vérifier qu'au moins un jour est ouvert
     */
    private function hasAtLeastOneOpenDay(): bool
    {
        foreach (JourSemaine::cases() as $jour) {
            if ($this->isOpenOn($jour)) {
                return true;
            }
        }

        return false;
    }
}
