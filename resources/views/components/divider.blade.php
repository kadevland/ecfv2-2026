{{--
    Divider Component - Preline UI Compatible

    Divider component for separating content with text or icons
    Based on: https://preline.co/docs/dividers.html

    Safelist Tailwind pour classes dynamiques :
    Thickness: border-t border-t-2 border-t-4 border-t-8 border-l border-l-2 border-l-4 border-l-8
    Colors: border-cinema-gold border-cinema-red border-cinema-black border-cinema-gold-light border-cinema-red-light
            border-admin-primary border-admin-secondary border-admin-accent border-admin-success border-admin-warning border-admin-error
    Text sizes: text-xs text-sm text-base text-lg text-xl text-2xl
    Text colors: text-cinema-gold text-cinema-red text-cinema-black text-cinema-gold-light text-cinema-red-light
                 text-admin-primary text-admin-secondary text-admin-accent text-admin-success text-admin-warning text-admin-error
    Backgrounds: bg-white bg-gray-100 bg-gray-200 bg-gray-300 bg-gray-800 bg-gray-900 bg-blue-600 bg-green-600 bg-red-600
    Opacity: border-opacity-10 border-opacity-20 border-opacity-30 border-opacity-40 border-opacity-50 border-opacity-60 border-opacity-70 border-opacity-80 border-opacity-90
             bg-opacity-10 bg-opacity-20 bg-opacity-30 bg-opacity-40 bg-opacity-50 bg-opacity-60 bg-opacity-70 bg-opacity-80 bg-opacity-90
--}}
@props([
    // Orientation & Type
    'orientation' => 'horizontal', // horizontal, vertical
    'variant' => 'line',          // line, dashed, dotted, double

    // Text/Content
    'text' => null,               // Text to display in divider
    'textAlign' => 'center',      // left, center, right
    'textSize' => null,           // xs, sm, base, lg, xl
    'textWeight' => null,         // light, normal, medium, semibold, bold
    'textColor' => null,          // Text color

    // Icon
    'icon' => null,               // Icon name for @svg
    'iconSize' => 'w-4 h-4',     // Icon size classes
    'iconColor' => null,          // Icon color

    // Styling
    'thickness' => '1',           // 1, 2, 4, 8 (border width)
    'color' => null,              // Couleur : primary, gold, red, gray-300, etc.
    'theme' => 'cinema',          // Theme : cinema, admin
    'opacity' => null,            // Border opacity
    'length' => null,             // Width/height control

    // Spacing
    'margin' => null,             // Margin around divider
    'padding' => null,            // Padding for text/icon area

    // Background
    'background' => null,         // Background color for text/icon area
    'backgroundOpacity' => null,  // Background opacity

    // Responsive
    'responsive' => null,         // Custom responsive classes
    'dark' => null,               // Dark mode variants

    // Custom Classes
    'class' => '',                // Additional classes
])

@php
    $isVertical = $orientation === 'vertical';
    $hasContent = $text !== null || $icon !== null;

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

    // === BASE CLASSES ===
    $baseClasses = [];
    $textClasses = [];
    $lineClasses = [];

    // === ORIENTATION ===
    if ($isVertical) {
        $baseClasses[] = 'flex flex-col items-center';
        if ($length !== null) {
            $baseClasses[] = str_starts_with($length, 'h-') ? $length : "h-{$length}";
        }
    } else {
        $baseClasses[] = 'flex items-center';
        if ($length !== null) {
            $baseClasses[] = str_starts_with($length, 'w-') ? $length : "w-{$length}";
        } else {
            $baseClasses[] = 'w-full';
        }
    }

    // === MARGIN ===
    if ($margin !== null) {
        $baseClasses[] = $margin;
    }

    // === LINE STYLING ===
    $borderProperty = $isVertical ? 'border-l' : 'border-t';

    // Thickness
    $thicknessClass = match($thickness) {
        '1' => $borderProperty,
        '2' => "{$borderProperty}-2",
        '4' => "{$borderProperty}-4",
        '8' => "{$borderProperty}-8",
        default => "{$borderProperty}-{$thickness}",
    };
    $lineClasses[] = $thicknessClass;

    // Variant
    if ($variant === 'dashed') {
        $lineClasses[] = 'border-dashed';
    } elseif ($variant === 'dotted') {
        $lineClasses[] = 'border-dotted';
    } elseif ($variant === 'double') {
        $lineClasses[] = 'border-double';
    }

    // Color
    if ($finalColor !== null) {
        $colorClass = str_starts_with($finalColor, 'border-') ? $finalColor : "border-{$finalColor}";
        $lineClasses[] = $colorClass;
    } else {
        $lineClasses[] = 'border-gray-300 dark:border-gray-700';
    }

    // Opacity
    if ($opacity !== null) {
        $opacityClass = str_starts_with($opacity, 'border-opacity-') ? $opacity : "border-opacity-{$opacity}";
        $lineClasses[] = $opacityClass;
    }

    // === TEXT/ICON STYLING ===
    if ($hasContent) {
        // Background
        if ($background !== null) {
            $bgClass = str_starts_with($background, 'bg-') ? $background : "bg-{$background}";
            $textClasses[] = $bgClass;
        } else {
            $textClasses[] = 'bg-white dark:bg-gray-900';
        }

        if ($backgroundOpacity !== null) {
            $bgOpacityClass = str_starts_with($backgroundOpacity, 'bg-opacity-') ? $backgroundOpacity : "bg-opacity-{$backgroundOpacity}";
            $textClasses[] = $bgOpacityClass;
        }

        // Padding
        if ($padding !== null) {
            $textClasses[] = $padding;
        } else {
            $textClasses[] = $isVertical ? 'py-2' : 'px-3';
        }

        // Text styling
        if ($text !== null) {
            if ($textSize !== null) {
                $textClasses[] = str_starts_with($textSize, 'text-') ? $textSize : "text-{$textSize}";
            } else {
                $textClasses[] = 'text-sm';
            }

            if ($textWeight !== null) {
                $weightClass = match($textWeight) {
                    'light' => 'font-light',
                    'normal' => 'font-normal',
                    'medium' => 'font-medium',
                    'semibold' => 'font-semibold',
                    'bold' => 'font-bold',
                    default => str_starts_with($textWeight, 'font-') ? $textWeight : "font-{$textWeight}",
                };
                $textClasses[] = $weightClass;
            }

            if ($textColor !== null) {
                $textColorClass = str_starts_with($textColor, 'text-') ? $textColor : "text-{$textColor}";
                $textClasses[] = $textColorClass;
            } else {
                $textClasses[] = 'text-gray-500 dark:text-gray-400';
            }
        }
    }

    // === RESPONSIVE & VARIANTS ===
    if ($responsive !== null) {
        $baseClasses[] = $responsive;
    }

    if ($dark !== null) {
        $baseClasses[] = $dark;
    }

    // === BUILD FINAL CLASSES ===
    $finalBaseClasses = collect($baseClasses)->push($class)->filter()->implode(' ');
    $finalLineClasses = collect($lineClasses)->filter()->implode(' ');
    $finalTextClasses = collect($textClasses)->filter()->implode(' ');
@endphp

<div {{ $attributes->twMerge($finalBaseClasses) }}>
    @if($hasContent && in_array($textAlign, ['left', 'center']) && !$isVertical)
        <div class="flex-1 {{ $finalLineClasses }}"></div>
    @endif

    @if($hasContent && $textAlign === 'center' && $isVertical)
        <div class="flex-1 {{ $finalLineClasses }}"></div>
    @endif

    @if($hasContent)
        <div class="{{ $finalTextClasses }}">
            @if($icon !== null)
                @svg($icon, ['class' => $iconSize . ($iconColor ? ' ' . $iconColor : '')])
            @endif

            @if($text !== null)
                {{ $text }}
            @endif
        </div>
    @endif

    @if($hasContent && in_array($textAlign, ['right', 'center']) && !$isVertical)
        <div class="flex-1 {{ $finalLineClasses }}"></div>
    @endif

    @if($hasContent && $textAlign === 'center' && $isVertical)
        <div class="flex-1 {{ $finalLineClasses }}"></div>
    @endif

    @if(!$hasContent)
        <div class="w-full {{ $finalLineClasses }}"></div>
    @endif
</div>