{{--
    Text Component - Preline UI Compatible

    Typography component using Tailwind text utilities
    Based on: https://preline.co/docs/typography.html

    Safelist Tailwind pour classes dynamiques :
    Text colors: text-cinema-gold text-cinema-red text-cinema-black text-cinema-gold-light text-cinema-red-light
                 text-admin-primary text-admin-secondary text-admin-accent text-admin-success text-admin-warning text-admin-error
--}}
@props([
    'as' => 'p',             // HTML element: p, span, div, h1-h6, strong, em, small, mark, etc.
    'size' => null,          // xs, sm, base, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl, 8xl, 9xl
    'weight' => null,        // thin, extralight, light, normal, medium, semibold, bold, extrabold, black
    'color' => null,         // Couleur : primary, gold, red, gray-900, etc.
    'theme' => 'cinema',     // Theme : cinema, admin
    'align' => null,         // left, center, right, justify, start, end
    'leading' => null,       // tight, snug, normal, relaxed, loose, 3-10
    'tracking' => null,      // tighter, tight, normal, wide, wider, widest
    'decoration' => null,    // underline, overline, line-through, no-underline
    'transform' => null,     // uppercase, lowercase, capitalize, normal-case
    'gradient' => false,     // Apply gradient text effect
    'truncate' => false,     // Truncate text with ellipsis
    'class' => '',           // Classes additionnelles
])

@php
    $baseClasses = [];

    // === SYSTÈME DE COULEURS THÉMATIQUES ===
    $themeColors = [
        'cinema' => [
            // Sémantique
            'primary' => 'cinema-gold',
            'secondary' => 'cinema-black',
            'accent' => 'cinema-gold-light',
            'danger' => 'cinema-red',
            'warning' => 'cinema-red-light',
            'success' => 'green-600',
            'info' => 'blue-600',
            // Shortcuts
            'gold' => 'cinema-gold',
            'black' => 'cinema-black',
            'gold-light' => 'cinema-gold-light',
            'red' => 'cinema-red',
            'red-light' => 'cinema-red-light',
        ],
        'admin' => [
            'primary' => 'admin-primary',
            'secondary' => 'admin-secondary',
            'accent' => 'admin-accent',
            'danger' => 'admin-error',
            'warning' => 'admin-warning',
            'success' => 'admin-success',
            'info' => 'admin-accent',
        ]
    ];

    // Résolution de la couleur
    $finalColor = null;
    if ($color !== null) {
        if (isset($themeColors[$theme][$color])) {
            // Couleur du mapping thématique
            $finalColor = $themeColors[$theme][$color];
        } else {
            // Couleur Tailwind directe
            $finalColor = $color;
        }
    }

    // Gérer la taille
    if ($size !== null) {
        $sizeClass = match($size) {
            'xs' => 'text-xs',
            'sm' => 'text-sm',
            'base' => 'text-base',
            'lg' => 'text-lg',
            'xl' => 'text-xl',
            '2xl' => 'text-2xl',
            '3xl' => 'text-3xl',
            '4xl' => 'text-4xl',
            '5xl' => 'text-5xl',
            '6xl' => 'text-6xl',
            '7xl' => 'text-7xl',
            '8xl' => 'text-8xl',
            '9xl' => 'text-9xl',
            default => $size,
        };
        $baseClasses[] = $sizeClass;
    }

    // Gérer le poids
    if ($weight !== null) {
        $weightClass = match($weight) {
            'thin' => 'font-thin',
            'extralight' => 'font-extralight',
            'light' => 'font-light',
            'normal' => 'font-normal',
            'medium' => 'font-medium',
            'semibold' => 'font-semibold',
            'bold' => 'font-bold',
            'extrabold' => 'font-extrabold',
            'black' => 'font-black',
            default => $weight,
        };
        $baseClasses[] = $weightClass;
    }

    // Gérer la couleur (ou gradient)
    if ($finalColor !== null && !$gradient) {
        $baseClasses[] = "text-{$finalColor}";
    }

    // Gérer l'alignement
    if ($align !== null) {
        $alignClass = match($align) {
            'left' => 'text-left',
            'center' => 'text-center',
            'right' => 'text-right',
            'justify' => 'text-justify',
            'start' => 'text-start',
            'end' => 'text-end',
            default => $align,
        };
        $baseClasses[] = $alignClass;
    }

    // Gérer l'interlignage
    if ($leading !== null) {
        $leadingClass = match($leading) {
            'tight' => 'leading-tight',
            'snug' => 'leading-snug',
            'normal' => 'leading-normal',
            'relaxed' => 'leading-relaxed',
            'loose' => 'leading-loose',
            default => "leading-{$leading}",
        };
        $baseClasses[] = $leadingClass;
    }

    // Gérer l'espacement des lettres
    if ($tracking !== null) {
        $trackingClass = match($tracking) {
            'tighter' => 'tracking-tighter',
            'tight' => 'tracking-tight',
            'normal' => 'tracking-normal',
            'wide' => 'tracking-wide',
            'wider' => 'tracking-wider',
            'widest' => 'tracking-widest',
            default => $tracking,
        };
        $baseClasses[] = $trackingClass;
    }

    // Gérer la décoration
    if ($decoration !== null) {
        $decorationClass = match($decoration) {
            'underline' => 'underline',
            'overline' => 'overline',
            'line-through' => 'line-through',
            'no-underline' => 'no-underline',
            default => $decoration,
        };
        $baseClasses[] = $decorationClass;
    }

    // Gérer la transformation
    if ($transform !== null) {
        $transformClass = match($transform) {
            'uppercase' => 'uppercase',
            'lowercase' => 'lowercase',
            'capitalize' => 'capitalize',
            'normal-case' => 'normal-case',
            default => $transform,
        };
        $baseClasses[] = $transformClass;
    }

    // Gérer le gradient
    if ($gradient) {
        $baseClasses[] = 'bg-gradient-to-r';
        if ($finalColor) {
            $baseClasses[] = $finalColor; // Should be gradient classes like 'from-blue-600 to-purple-600'
        }
        $baseClasses[] = 'bg-clip-text text-transparent';
    }

    // Gérer la troncature
    if ($truncate) {
        $baseClasses[] = 'truncate';
    }

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');
@endphp

<{{ $as }} {{ $attributes->twMerge($finalClasses) }}>
    {{ $slot }}
</{{ $as }}>