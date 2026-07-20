<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class SalleCapacityCoherence implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $nombreRangees   = (int) ($this->data['nombre_rangees'] ?? 0);
        $placesParRangee = (int) ($this->data['places_par_rangee'] ?? 0);
        $placesStandard  = (int) ($this->data['places_standard'] ?? 0);
        $placesPremium   = (int) ($this->data['places_premium'] ?? 0);
        $placesPmr       = (int) ($this->data['places_pmr'] ?? 0);
        $capaciteTotale  = (int) $value;

        // Vérification 1: capacité calculée = nombre_rangees × places_par_rangee
        $capaciteCalculee = $nombreRangees * $placesParRangee;

        // Vérification 2: somme des types de places = capacité totale
        $sommePlaces = $placesStandard + $placesPremium + $placesPmr;

        // Si les places par type sont renseignées, elles doivent correspondre à la capacité
        if ($sommePlaces > 0 && $sommePlaces !== $capaciteTotale) {
            $fail("La somme des places (Standard: {$placesStandard} + Premium: {$placesPremium} + PMR: {$placesPmr} = {$sommePlaces}) doit égaler la capacité totale ({$capaciteTotale}).");

            return;
        }

        // Si nombre de rangées et places par rangée sont renseignés, ils doivent correspondre à la capacité
        if ($nombreRangees > 0 && $placesParRangee > 0 && $capaciteCalculee !== $capaciteTotale) {
            $fail("La capacité calculée (Rangées: {$nombreRangees} × Places/rangée: {$placesParRangee} = {$capaciteCalculee}) doit égaler la capacité totale ({$capaciteTotale}).");

            return;
        }

        // Vérification 3: cohérence globale si tous les champs sont renseignés
        if ($nombreRangees > 0 && $placesParRangee > 0 && $sommePlaces > 0) {
            if ($capaciteCalculee !== $sommePlaces) {
                $fail("Incohérence: Capacité calculée ({$capaciteCalculee}) ≠ Somme des places par type ({$sommePlaces}). Vérifiez vos saisies.");
            }
        }
    }
}
