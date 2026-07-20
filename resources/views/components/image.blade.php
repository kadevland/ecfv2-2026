{{--
    Image Component - Preline UI Compatible

    Image component with responsive utilities and styling options
    Based on: https://preline.co/docs/images.html

    Safelist Tailwind pour classes dynamiques :
    Opacity: opacity-0 opacity-5 opacity-10 opacity-20 opacity-25 opacity-30 opacity-40 opacity-50 opacity-60 opacity-70 opacity-75 opacity-80 opacity-90 opacity-95 opacity-100
    Duration: duration-75 duration-100 duration-150 duration-200 duration-300 duration-500 duration-700 duration-1000
--}}
@props([
    // Core Image Properties
    'src' => '',             // Image source (required)
    'alt' => '',             // Alt text for accessibility
    'title' => null,         // Title attribute
    'loading' => 'lazy',     // lazy, eager
    'decoding' => null,      // sync, async, auto
    'fetchpriority' => null, // high, low, auto

    // Layout & Sizing
    'width' => null,         // Width utility class (w-56, w-full, etc.)
    'height' => null,        // Height utility class (h-56, h-auto, etc.)
    'size' => null,          // Combined width/height for square images
    'minWidth' => null,      // min-w-0, min-w-full, etc.
    'minHeight' => null,     // min-h-0, min-h-full, etc.
    'maxWidth' => null,      // max-w-xs, max-w-full, etc.
    'maxHeight' => null,     // max-h-xs, max-h-full, etc.
    'aspectRatio' => null,   // square, video, auto, [3/4], [4/3], [16/9], etc.
    'display' => null,       // block, inline-block, inline, flex, grid

    // Object Properties
    'objectFit' => null,     // cover, contain, fill, scale-down, none
    'objectPosition' => null, // center, top, bottom, left, right, custom

    // Visual Effects
    'rounded' => null,       // none, sm, md, lg, xl, 2xl, 3xl, full
    'border' => null,        // border width and color
    'shadow' => null,        // shadow utilities
    'opacity' => null,       // opacity-0 to opacity-100

    // Transform & Animation
    'scale' => null,         // scale-50 to scale-150
    'rotate' => null,        // rotate-0, rotate-45, rotate-90, etc.
    'translate' => null,     // translate-x-1, translate-y-1, etc.
    'skew' => null,          // skew-x-1, skew-y-1, etc.
    'origin' => null,        // origin-center, origin-top, etc.

    // Hover & Focus States
    'hover' => null,         // hover:scale-105, hover:brightness-110, etc.
    'focus' => null,         // focus states
    'active' => null,        // active states
    'group' => null,         // group hover effects

    // Transitions & Animations
    'transition' => null,    // transition properties (true = default, custom string)
    'duration' => null,      // duration-75, duration-300, etc.
    'ease' => null,          // ease-in, ease-out, ease-in-out, ease-linear
    'delay' => null,         // delay-75, delay-100, etc.
    'animate' => null,       // animate-spin, animate-pulse, etc.

    // Filters & Effects
    'filter' => null,        // Enable filters
    'blur' => null,          // blur-none, blur-sm, blur, blur-md, etc.
    'brightness' => null,    // brightness-0 to brightness-200
    'contrast' => null,      // contrast-0 to contrast-200
    'grayscale' => null,     // grayscale-0, grayscale
    'hueRotate' => null,     // hue-rotate-0, hue-rotate-15, etc.
    'invert' => null,        // invert-0, invert
    'saturate' => null,      // saturate-0 to saturate-200
    'sepia' => null,         // sepia-0, sepia
    'dropShadow' => null,    // drop-shadow utilities
    'backdrop' => null,      // backdrop-blur, backdrop-brightness, etc.

    // Responsive & State Management
    'responsive' => null,    // Custom responsive classes
    'dark' => null,          // Dark mode variants
    'print' => null,         // Print-specific styles

    // Accessibility & SEO
    'role' => null,          // ARIA role
    'tabindex' => null,      // Tab index
    'draggable' => null,     // true, false

    // Advanced Features
    'overlay' => false,      // Add overlay capabilities
    'fallback' => null,      // Fallback image src
    'placeholder' => null,   // Placeholder/loading state
    'retina' => null,        // High DPI image src
    'webp' => null,          // WebP format src
    'avif' => null,          // AVIF format src

    // Custom Classes
    'effect' => null,        // Custom combined effects
    'class' => '',           // Additional classes
])

@php
    $baseClasses = [];
    $attributes_array = [];

    // === DISPLAY ===
    if ($display !== null) {
        $baseClasses[] = match($display) {
            'block' => 'block',
            'inline-block' => 'inline-block',
            'inline' => 'inline',
            'flex' => 'flex',
            'grid' => 'grid',
            default => $display,
        };
    } else {
        $baseClasses[] = 'block'; // Default
    }

    // === SIZING ===
    if ($size !== null) {
        $baseClasses[] = "w-{$size}";
        $baseClasses[] = "h-{$size}";
    } else {
        if ($width !== null) {
            $baseClasses[] = str_starts_with($width, 'w-') ? $width : "w-{$width}";
        }
        if ($height !== null) {
            $baseClasses[] = str_starts_with($height, 'h-') ? $height : "h-{$height}";
        }
    }

    if ($minWidth !== null) $baseClasses[] = str_starts_with($minWidth, 'min-w-') ? $minWidth : "min-w-{$minWidth}";
    if ($minHeight !== null) $baseClasses[] = str_starts_with($minHeight, 'min-h-') ? $minHeight : "min-h-{$minHeight}";
    if ($maxWidth !== null) $baseClasses[] = str_starts_with($maxWidth, 'max-w-') ? $maxWidth : "max-w-{$maxWidth}";
    if ($maxHeight !== null) $baseClasses[] = str_starts_with($maxHeight, 'max-h-') ? $maxHeight : "max-h-{$maxHeight}";

    // === ASPECT RATIO ===
    if ($aspectRatio !== null) {
        $baseClasses[] = match($aspectRatio) {
            'square' => 'aspect-square',
            'video' => 'aspect-video',
            'auto' => 'aspect-auto',
            default => str_starts_with($aspectRatio, 'aspect-') ? $aspectRatio : "aspect-{$aspectRatio}",
        };
    }

    // === OBJECT PROPERTIES ===
    if ($objectFit !== null) {
        $baseClasses[] = match($objectFit) {
            'cover' => 'object-cover',
            'contain' => 'object-contain',
            'fill' => 'object-fill',
            'scale-down' => 'object-scale-down',
            'none' => 'object-none',
            default => $objectFit,
        };
    }

    if ($objectPosition !== null) {
        $baseClasses[] = match($objectPosition) {
            'center' => 'object-center',
            'top' => 'object-top',
            'bottom' => 'object-bottom',
            'left' => 'object-left',
            'right' => 'object-right',
            'left-top' => 'object-left-top',
            'left-bottom' => 'object-left-bottom',
            'right-top' => 'object-right-top',
            'right-bottom' => 'object-right-bottom',
            default => str_starts_with($objectPosition, 'object-') ? $objectPosition : "object-{$objectPosition}",
        };
    }

    // === VISUAL EFFECTS ===
    if ($rounded !== null) {
        $baseClasses[] = match($rounded) {
            'none' => 'rounded-none',
            'sm' => 'rounded-sm',
            'md' => 'rounded-md',
            'lg' => 'rounded-lg',
            'xl' => 'rounded-xl',
            '2xl' => 'rounded-2xl',
            '3xl' => 'rounded-3xl',
            'full' => 'rounded-full',
            default => str_starts_with($rounded, 'rounded-') ? $rounded : "rounded-{$rounded}",
        };
    }

    if ($border !== null) $baseClasses[] = $border;
    if ($shadow !== null) $baseClasses[] = str_starts_with($shadow, 'shadow') ? $shadow : "shadow-{$shadow}";
    if ($opacity !== null) $baseClasses[] = str_starts_with($opacity, 'opacity-') ? $opacity : "opacity-{$opacity}";

    // === TRANSFORMS ===
    if ($scale !== null) $baseClasses[] = str_starts_with($scale, 'scale-') ? $scale : "scale-{$scale}";
    if ($rotate !== null) $baseClasses[] = str_starts_with($rotate, 'rotate-') ? $rotate : "rotate-{$rotate}";
    if ($translate !== null) $baseClasses[] = $translate;
    if ($skew !== null) $baseClasses[] = $skew;
    if ($origin !== null) $baseClasses[] = str_starts_with($origin, 'origin-') ? $origin : "origin-{$origin}";

    // === STATES ===
    if ($hover !== null) $baseClasses[] = $hover;
    if ($focus !== null) $baseClasses[] = $focus;
    if ($active !== null) $baseClasses[] = $active;
    if ($group !== null) $baseClasses[] = $group;

    // === TRANSITIONS ===
    if ($transition !== null) {
        if ($transition === true) {
            $baseClasses[] = 'transition-all';
        } else {
            $baseClasses[] = $transition;
        }
    }
    if ($duration !== null) $baseClasses[] = str_starts_with($duration, 'duration-') ? $duration : "duration-{$duration}";
    if ($ease !== null) $baseClasses[] = str_starts_with($ease, 'ease-') ? $ease : "ease-{$ease}";
    if ($delay !== null) $baseClasses[] = str_starts_with($delay, 'delay-') ? $delay : "delay-{$delay}";
    if ($animate !== null) $baseClasses[] = str_starts_with($animate, 'animate-') ? $animate : "animate-{$animate}";

    // === FILTERS ===
    if ($filter !== null && $filter !== false) $baseClasses[] = is_string($filter) ? $filter : 'filter';
    if ($blur !== null) $baseClasses[] = str_starts_with($blur, 'blur') ? $blur : "blur-{$blur}";
    if ($brightness !== null) $baseClasses[] = str_starts_with($brightness, 'brightness-') ? $brightness : "brightness-{$brightness}";
    if ($contrast !== null) $baseClasses[] = str_starts_with($contrast, 'contrast-') ? $contrast : "contrast-{$contrast}";
    if ($grayscale !== null) $baseClasses[] = $grayscale === true ? 'grayscale' : $grayscale;
    if ($hueRotate !== null) $baseClasses[] = str_starts_with($hueRotate, 'hue-rotate-') ? $hueRotate : "hue-rotate-{$hueRotate}";
    if ($invert !== null) $baseClasses[] = $invert === true ? 'invert' : $invert;
    if ($saturate !== null) $baseClasses[] = str_starts_with($saturate, 'saturate-') ? $saturate : "saturate-{$saturate}";
    if ($sepia !== null) $baseClasses[] = $sepia === true ? 'sepia' : $sepia;
    if ($dropShadow !== null) $baseClasses[] = str_starts_with($dropShadow, 'drop-shadow') ? $dropShadow : "drop-shadow-{$dropShadow}";
    if ($backdrop !== null) $baseClasses[] = $backdrop;

    // === RESPONSIVE & VARIANTS ===
    if ($responsive !== null) $baseClasses[] = $responsive;
    if ($dark !== null) $baseClasses[] = $dark;
    if ($print !== null) $baseClasses[] = $print;

    // === OVERLAY ===
    if ($overlay) $baseClasses[] = 'relative';

    // === CUSTOM EFFECTS ===
    if ($effect !== null) $baseClasses[] = $effect;

    // === HTML ATTRIBUTES ===
    if ($title !== null) $attributes_array['title'] = $title;
    if ($decoding !== null) $attributes_array['decoding'] = $decoding;
    if ($fetchpriority !== null) $attributes_array['fetchpriority'] = $fetchpriority;
    if ($role !== null) $attributes_array['role'] = $role;
    if ($tabindex !== null) $attributes_array['tabindex'] = $tabindex;
    if ($draggable !== null) $attributes_array['draggable'] = $draggable;

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');
@endphp

@if($overlay)
    <div {{ $attributes->twMerge($finalClasses) }}>
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            loading="{{ $loading }}"
            @foreach($attributes_array as $attr => $value)
                {{ $attr }}="{{ $value }}"
            @endforeach
            class="w-full h-full object-cover"
        >
        @if($slot->isNotEmpty())
            <div class="absolute inset-0 flex items-center justify-center">
                {{ $slot }}
            </div>
        @endif
    </div>
@else
    <img
        {{ $attributes->twMerge($finalClasses) }}
        src="{{ $src }}"
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        @foreach($attributes_array as $attr => $value)
            {{ $attr }}="{{ $value }}"
        @endforeach
    >
@endif