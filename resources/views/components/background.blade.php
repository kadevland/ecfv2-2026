{{--
    Background Component - Preline UI Compatible

    Background image component with attachment and positioning
    Based on: https://preline.co/docs/images.html

    Safelist Tailwind pour classes dynamiques :
    Positions: bg-center bg-top bg-bottom bg-left bg-right bg-left-top bg-left-bottom bg-right-top bg-right-bottom
    Sizes: bg-auto bg-cover bg-contain
    Opacity: opacity-0 opacity-10 opacity-20 opacity-30 opacity-40 opacity-50 opacity-60 opacity-70 opacity-80 opacity-90 opacity-100
--}}
@props([
    // Background Image Properties
    'image' => '',           // Background image URL
    'attachment' => null,    // fixed, local, scroll
    'bgPosition' => null,      // center, top, bottom, left, right, left-top, etc.
    'size' => null,          // cover, contain, auto, custom
    'repeat' => null,        // repeat, no-repeat, repeat-x, repeat-y, round, space
    'origin' => null,        // border, padding, content
    'clip' => null,          // border, padding, content, text

    // Layout & Sizing
    'width' => null,         // w-full, w-screen, w-96, etc.
    'height' => null,        // h-screen, h-full, h-96, etc.
    'minWidth' => null,      // min-w-0, min-w-full, etc.
    'minHeight' => null,     // min-h-0, min-h-screen, etc.
    'maxWidth' => null,      // max-w-xs, max-w-full, etc.
    'maxHeight' => null,     // max-h-xs, max-h-screen, etc.

    // Visual Effects
    'gradient' => null,      // Gradient overlay classes
    'overlay' => null,       // Color overlay with opacity
    'opacity' => null,       // Background opacity
    'blur' => null,          // Backdrop blur effect
    'brightness' => null,    // Backdrop brightness
    'contrast' => null,      // Backdrop contrast
    'saturate' => null,      // Backdrop saturate

    // Display & Position
    'display' => null,       // block, flex, grid, inline-block, etc.
    'position' => null,      // relative, absolute, fixed, sticky
    'zIndex' => null,        // z-0, z-10, z-50, etc.

    // Responsive & Variants
    'responsive' => null,    // Custom responsive classes
    'dark' => null,          // Dark mode variants

    // Custom Classes
    'class' => '',           // Additional classes
])

@php
    $baseClasses = [];

    // === DISPLAY & LAYOUT ===
    if ($display !== null) {
        $baseClasses[] = match($display) {
            'block' => 'block',
            'flex' => 'flex',
            'grid' => 'grid',
            'inline-block' => 'inline-block',
            'inline' => 'inline',
            default => $display,
        };
    }

    if ($position !== null) {
        $baseClasses[] = match($position) {
            'relative' => 'relative',
            'absolute' => 'absolute',
            'fixed' => 'fixed',
            'sticky' => 'sticky',
            'static' => 'static',
            default => $position,
        };
    }

    // === SIZING ===
    if ($width !== null) {
        $baseClasses[] = str_starts_with($width, 'w-') ? $width : "w-{$width}";
    }

    if ($height !== null) {
        $baseClasses[] = str_starts_with($height, 'h-') ? $height : "h-{$height}";
    }

    if ($minWidth !== null) {
        $baseClasses[] = str_starts_with($minWidth, 'min-w-') ? $minWidth : "min-w-{$minWidth}";
    }

    if ($minHeight !== null) {
        $baseClasses[] = str_starts_with($minHeight, 'min-h-') ? $minHeight : "min-h-{$minHeight}";
    }

    if ($maxWidth !== null) {
        $baseClasses[] = str_starts_with($maxWidth, 'max-w-') ? $maxWidth : "max-w-{$maxWidth}";
    }

    if ($maxHeight !== null) {
        $baseClasses[] = str_starts_with($maxHeight, 'max-h-') ? $maxHeight : "max-h-{$maxHeight}";
    }

    // === Z-INDEX ===
    if ($zIndex !== null) {
        $baseClasses[] = str_starts_with($zIndex, 'z-') ? $zIndex : "z-{$zIndex}";
    }

    // === BACKGROUND IMAGE PROPERTIES ===
    if ($image) {
        // Default size si pas spécifié
        if ($size === null) {
            $baseClasses[] = 'bg-cover';
        }
    }

    // Background attachment
    if ($attachment !== null) {
        $baseClasses[] = match($attachment) {
            'fixed' => 'bg-fixed',
            'local' => 'bg-local',
            'scroll' => 'bg-scroll',
            default => $attachment,
        };
    }

    // Background position (renommé bgPosition pour éviter conflit avec position CSS)
    if (isset($bgPosition) || (isset($position) && in_array($position, ['center', 'top', 'bottom', 'left', 'right']))) {
        $pos = $bgPosition ?? $position;
        $baseClasses[] = match($pos) {
            'center' => 'bg-center',
            'top' => 'bg-top',
            'bottom' => 'bg-bottom',
            'left' => 'bg-left',
            'right' => 'bg-right',
            'left-top' => 'bg-left-top',
            'left-bottom' => 'bg-left-bottom',
            'right-top' => 'bg-right-top',
            'right-bottom' => 'bg-right-bottom',
            default => str_starts_with($pos, 'bg-') ? $pos : "bg-{$pos}",
        };
    }

    // Background size
    if ($size !== null) {
        $baseClasses[] = match($size) {
            'cover' => 'bg-cover',
            'contain' => 'bg-contain',
            'auto' => 'bg-auto',
            default => str_starts_with($size, 'bg-') ? $size : "bg-{$size}",
        };
    }

    // Background repeat
    if ($repeat !== null) {
        $baseClasses[] = match($repeat) {
            'repeat' => 'bg-repeat',
            'no-repeat' => 'bg-no-repeat',
            'repeat-x' => 'bg-repeat-x',
            'repeat-y' => 'bg-repeat-y',
            'round' => 'bg-repeat-round',
            'space' => 'bg-repeat-space',
            default => $repeat,
        };
    }

    // Background origin
    if ($origin !== null) {
        $baseClasses[] = match($origin) {
            'border' => 'bg-origin-border',
            'padding' => 'bg-origin-padding',
            'content' => 'bg-origin-content',
            default => $origin,
        };
    }

    // Background clip
    if ($clip !== null) {
        $baseClasses[] = match($clip) {
            'border' => 'bg-clip-border',
            'padding' => 'bg-clip-padding',
            'content' => 'bg-clip-content',
            'text' => 'bg-clip-text',
            default => $clip,
        };
    }

    // === VISUAL EFFECTS ===
    if ($opacity !== null) {
        $baseClasses[] = str_starts_with($opacity, 'opacity-') ? $opacity : "opacity-{$opacity}";
    }

    // Backdrop effects
    if ($blur !== null) {
        $baseClasses[] = str_starts_with($blur, 'backdrop-blur') ? $blur : "backdrop-blur-{$blur}";
    }

    if ($brightness !== null) {
        $baseClasses[] = str_starts_with($brightness, 'backdrop-brightness') ? $brightness : "backdrop-brightness-{$brightness}";
    }

    if ($contrast !== null) {
        $baseClasses[] = str_starts_with($contrast, 'backdrop-contrast') ? $contrast : "backdrop-contrast-{$contrast}";
    }

    if ($saturate !== null) {
        $baseClasses[] = str_starts_with($saturate, 'backdrop-saturate') ? $saturate : "backdrop-saturate-{$saturate}";
    }

    // === OVERLAYS ===
    if ($gradient) {
        $baseClasses[] = $gradient; // Ex: "bg-gradient-to-r from-black/50 to-transparent"
    }

    if ($overlay) {
        $baseClasses[] = $overlay; // Ex: "bg-black/30"
    }

    // === RESPONSIVE & VARIANTS ===
    if ($responsive !== null) {
        $baseClasses[] = $responsive;
    }

    if ($dark !== null) {
        $baseClasses[] = $dark;
    }

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');

    // Style inline pour l'image
    $style = $image ? "background-image: url('{$image}');" : '';
@endphp

<div
    {{ $attributes->twMerge($finalClasses) }}
    @if($style) style="{{ $style }}" @endif
>
    {{ $slot }}
</div>
