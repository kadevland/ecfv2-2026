{{--
    Link Component - Preline UI Compatible

    Link component with advanced styling and Laravel routing
    Based on: https://preline.co/docs/links.html

    Safelist Tailwind pour classes dynamiques :
    Gaps: gap-1 gap-2 gap-3 gap-4 gap-5 gap-6
    Text sizes: text-xs text-sm text-base text-lg text-xl text-2xl text-3xl
    Text colors: text-cinema-gold text-cinema-red text-cinema-black text-cinema-gold-light text-cinema-red-light
                 text-admin-primary text-admin-secondary text-admin-accent text-admin-success text-admin-warning text-admin-error
    Underline offset: underline-offset-1 underline-offset-2 underline-offset-4 underline-offset-8
    Opacity: opacity-10 opacity-20 opacity-30 opacity-40 opacity-50 opacity-60 opacity-70 opacity-80 opacity-90
    Duration: duration-75 duration-100 duration-150 duration-200 duration-300 duration-500 duration-700 duration-1000
    Decoration colors: decoration-cinema-gold decoration-cinema-red decoration-cinema-black
--}}
@props([
    // Link Target
    'href' => '#',           // URL or route
    'route' => null,         // Laravel route name
    'routeParams' => [],     // Route parameters
    'external' => false,     // External link detection
    'target' => null,        // _blank, _self, _parent, _top
    'rel' => null,           // noopener, noreferrer, etc.

    // Typography
    'size' => null,          // xs, sm, base, lg, xl, 2xl, etc.
    'weight' => null,        // light, normal, medium, semibold, bold, etc.
    'color' => null,         // Couleur : primary, gold, red, blue-600, etc.
    'theme' => 'cinema',     // Theme : cinema, admin

    // Visual Effects
    'underline' => null,     // true, false, hover, focus, always
    'underlineOffset' => null, // 1, 2, 4, 8
    'underlineColor' => null, // Custom underline color
    'opacity' => null,       // 10-90

    // States
    'hover' => null,         // hover:text-blue-600, etc.
    'focus' => null,         // focus styles
    'active' => null,        // active styles
    'disabled' => false,     // Disabled state

    // Transitions
    'transition' => null,    // transition properties
    'duration' => null,      // duration-150, etc.
    'ease' => null,          // ease-in, ease-out, etc.

    // Icons
    'icon' => null,          // Icon name for @svg
    'iconPosition' => 'left', // left, right
    'iconSize' => 'w-4 h-4', // Icon size classes
    'iconClass' => '',       // Additional icon classes

    // Layout
    'display' => null,       // inline, inline-block, block, flex
    'gap' => null,           // Gap between icon and text (when flex)

    // Responsive & Variants
    'responsive' => null,    // Custom responsive classes
    'dark' => null,          // Dark mode variants

    // Custom Classes
    'class' => '',           // Additional classes
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

    // === DISPLAY ===
    $hasIcon = $icon !== null;

    if ($display !== null) {
        $baseClasses[] = $display;
    } elseif ($hasIcon) {
        $baseClasses[] = 'inline-flex items-center';
    } else {
        $baseClasses[] = 'inline';
    }

    // === GAP FOR ICONS ===
    if ($hasIcon && $gap !== null) {
        $baseClasses[] = str_starts_with($gap, 'gap-') ? $gap : "gap-{$gap}";
    } elseif ($hasIcon) {
        $baseClasses[] = 'gap-1'; // Default gap
    }

    // === TYPOGRAPHY ===
    if ($size !== null) {
        $baseClasses[] = str_starts_with($size, 'text-') ? $size : "text-{$size}";
    }

    if ($weight !== null) {
        $baseClasses[] = match($weight) {
            'light' => 'font-light',
            'normal' => 'font-normal',
            'medium' => 'font-medium',
            'semibold' => 'font-semibold',
            'bold' => 'font-bold',
            default => str_starts_with($weight, 'font-') ? $weight : "font-{$weight}",
        };
    }

    // === COULEUR ===
    if ($finalColor !== null) {
        $baseClasses[] = str_starts_with($finalColor, 'text-') ? $finalColor : "text-{$finalColor}";
    }

    // === UNDERLINE ===
    if ($underline !== null) {
        $underlineClass = match($underline) {
            true => 'underline',
            'always' => 'underline',
            'hover' => 'hover:underline',
            'focus' => 'focus:underline',
            false => 'no-underline',
            default => $underline,
        };
        $baseClasses[] = $underlineClass;
    }

    if ($underlineOffset !== null) {
        $baseClasses[] = "underline-offset-{$underlineOffset}";
    }

    if ($underlineColor !== null) {
        $baseClasses[] = str_starts_with($underlineColor, 'decoration-') ? $underlineColor : "decoration-{$underlineColor}";
    }

    // === OPACITY ===
    if ($opacity !== null) {
        $baseClasses[] = str_starts_with($opacity, 'opacity-') ? $opacity : "opacity-{$opacity}";
    }

    // === STATES ===
    if ($hover !== null) {
        $baseClasses[] = $hover;
    }

    if ($focus !== null) {
        $baseClasses[] = $focus;
    }

    if ($active !== null) {
        $baseClasses[] = $active;
    }

    if ($disabled) {
        $baseClasses[] = 'opacity-50 cursor-not-allowed pointer-events-none';
    }

    // === TRANSITIONS ===
    if ($transition !== null) {
        if ($transition === true) {
            $baseClasses[] = 'transition-colors';
        } else {
            $baseClasses[] = $transition;
        }
    }

    if ($duration !== null) {
        $baseClasses[] = str_starts_with($duration, 'duration-') ? $duration : "duration-{$duration}";
    }

    if ($ease !== null) {
        $baseClasses[] = str_starts_with($ease, 'ease-') ? $ease : "ease-{$ease}";
    }

    // === RESPONSIVE & VARIANTS ===
    if ($responsive !== null) {
        $baseClasses[] = $responsive;
    }

    if ($dark !== null) {
        $baseClasses[] = $dark;
    }

    // === URL GENERATION ===
    if ($route !== null) {
        $finalHref = route($route, $routeParams);
    } else {
        $finalHref = $href;
    }

    // === EXTERNAL LINK DETECTION ===
    if ($external === true || (is_string($finalHref) && (str_starts_with($finalHref, 'http') || str_starts_with($finalHref, '//')))) {
        $external = true;
        if ($target === null) $target = '_blank';
        if ($rel === null) $rel = 'noopener noreferrer';
    }

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');

    // === ATTRIBUTES ===
    $linkAttributes = ['href' => $finalHref];
    if ($target !== null) $linkAttributes['target'] = $target;
    if ($rel !== null) $linkAttributes['rel'] = $rel;
@endphp

@if($disabled)
    <span {{ $attributes->merge(['aria-disabled'=>true])->twMerge($finalClasses) }}>
        @if($hasIcon && $iconPosition === 'left')
            @svg($icon, ['class' => $iconSize . ' ' . $iconClass])
        @endif
        {{ $slot }}
        @if($hasIcon && $iconPosition === 'right')
            @svg($icon, ['class' => $iconSize . ' ' . $iconClass])
        @endif
    </span>
@else
    <a
        {{ $attributes->merge($linkAttributes)->twMerge($finalClasses) }}
        {{-- href="{{ $finalHref }}"
        @foreach($linkAttributes as $attr => $value)
            {{ $attr }}="{{ $value }}"
        @endforeach --}}
    >
        @if($hasIcon && $iconPosition === 'left')
            @svg($icon, ['class' => $iconSize . ' ' . $iconClass])
        @endif
        {{ $slot }}
        @if($hasIcon && $iconPosition === 'right')
            @svg($icon, ['class' => $iconSize . ' ' . $iconClass])
        @endif
    </a>
@endif
