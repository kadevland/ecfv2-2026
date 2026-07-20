{{--
    Heading Component - Preline UI Compatible

    Heading component with predefined styles for h1-h6
    Based on: https://preline.co/docs/typography.html
--}}
@props([
    'level' => 1,            // 1-6 for h1-h6
    'size' => null,          // Override default size for level
    'weight' => null,        // Override default weight
    'color' => null,         // Text color
    'gradient' => false,     // Apply gradient text
    'class' => '',           // Classes additionnelles
])

@php
    // Définir les styles par défaut pour chaque niveau
    $headingDefaults = [
        1 => ['size' => '4xl', 'weight' => 'bold'],
        2 => ['size' => '3xl', 'weight' => 'bold'],
        3 => ['size' => '2xl', 'weight' => 'semibold'],
        4 => ['size' => 'xl', 'weight' => 'semibold'],
        5 => ['size' => 'lg', 'weight' => 'medium'],
        6 => ['size' => 'base', 'weight' => 'medium'],
    ];

    $defaults = $headingDefaults[$level] ?? $headingDefaults[1];

    // Utiliser les overrides ou les defaults
    $finalSize = $size ?? $defaults['size'];
    $finalWeight = $weight ?? $defaults['weight'];
    $tag = "h{$level}";
@endphp

<x-text
    :as="$tag"
    :size="$finalSize"
    :weight="$finalWeight"
    :color="$color"
    :gradient="$gradient"
    :class="$class"
    {{ $attributes }}
>
    {{ $slot }}
</x-text>