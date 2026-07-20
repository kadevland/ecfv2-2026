{{--
    Brand Logo Component - Cinéphoria

    Logo SVG centralisé pour cohérence dans toute l'application
    Usage: <x-brand-logo size="sm" color="primary" />
--}}
@props([
    'size' => 'md',        // xs, sm, md, lg, xl
    'color' => 'primary',  // primary, white, gray, accent
    'class' => '',
])

@php
    $sizeClasses = match($size) {
        'xs' => 'w-4 h-4',
        'sm' => 'w-5 h-5',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-12 h-12',
        default => $size, // Pour classes custom comme w-16 h-16
    };

    $colorClasses = match($color) {
        'primary' => 'text-cinema-gold',
        'white' => 'text-white',
        'gray' => 'text-gray-400',
        'accent' => 'text-admin-accent',
        'black' => 'text-cinema-black',
        default => $color, // Pour classes custom
    };

    $finalClasses = collect([$sizeClasses, $colorClasses, $class])
        ->filter()
        ->implode(' ');
@endphp

<svg {{ $attributes->twMerge($finalClasses) }}
     fill="currentColor"
     viewBox="0 0 24 24"
     aria-label="Logo Cinéphoria">
    <path d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V7h2v2z"/>
</svg>