<?php

declare(strict_types=1);

use App\Domain\Enums\ClassificationFilm;

/**
 * Convert classification code to readable label
 *
 * @param string|null $classification
 * @return string
 */
if (!function_exists('classificationLabel')) {
    function classificationLabel(?string $classification): string
    {
        if (!$classification) {
            return 'Non classé';
        }

        try {
            $classificationEnum = ClassificationFilm::from($classification);

            return $classificationEnum->label();
        } catch (ValueError) {
            // Si la classification n'est pas trouvée, retourner la valeur brute
            return $classification;
        }
    }
}

/**
 * Get classification color class for styling
 *
 * @param string|null $classification
 * @return string
 */
if (!function_exists('classificationColorClass')) {
    function classificationColorClass(?string $classification): string
    {
        if (!$classification) {
            return 'text-gray-500';
        }

        try {
            $classificationEnum = ClassificationFilm::from($classification);

            return $classificationEnum->getColorClass();
        } catch (ValueError) {
            return 'text-gray-500';
        }
    }
}

/**
 * Get classification with styled span
 *
 * @param string|null $classification
 * @param string $extraClasses
 * @return string
 */
if (!function_exists('classificationBadge')) {
    function classificationBadge(?string $classification, string $extraClasses = 'px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium'): string
    {
        if (!$classification) {
            return '';
        }

        $label      = classificationLabel($classification);
        $colorClass = classificationColorClass($classification);

        return "<span class=\"{$extraClasses} {$colorClass}\">{$label}</span>";
    }
}
