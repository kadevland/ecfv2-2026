{{--
    Scrollbar Component - Preline UI Compatible

    Custom scrollbar styling component
    Based on: https://preline.co/docs/custom-scrollbar.html

    Safelist Tailwind pour classes dynamiques :
    Scrollbar width: scrollbar-thin scrollbar-none scrollbar-w-2 scrollbar-w-4
    Scrollbar colors: scrollbar-track-gray-100 scrollbar-thumb-gray-300 scrollbar-track-cinema-black scrollbar-thumb-cinema-gold
    Track colors: scrollbar-track-transparent scrollbar-track-gray-50 scrollbar-track-gray-100 scrollbar-track-gray-200
    Thumb colors: scrollbar-thumb-gray-300 scrollbar-thumb-gray-400 scrollbar-thumb-gray-500 scrollbar-thumb-cinema-gold scrollbar-thumb-cinema-red
    Corner colors: scrollbar-corner-gray-100 scrollbar-corner-transparent scrollbar-corner-cinema-black
--}}
@props([
    // Scrollbar Display
    'scrollbar' => 'default',   // default, thin, none, auto
    'scrollbarWidth' => null,            // Scrollbar width (2, 4, 6, etc.)

    // Track Styling
    'track' => null,            // Track background color
    'trackRadius' => null,      // Track border radius
    'trackHover' => null,       // Track hover state

    // Thumb Styling
    'thumb' => null,            // Thumb color
    'thumbRadius' => null,      // Thumb border radius
    'thumbHover' => null,       // Thumb hover state
    'thumbActive' => null,      // Thumb active state

    // Corner Styling
    'corner' => null,           // Corner color

    // Container Properties
    'height' => null,           // Container height
    'maxHeight' => null,        // Max height
    'width' => null,            // Container width
    'maxWidth' => null,         // Max width

    // Overflow Behavior
    'overflow' => 'auto',       // auto, scroll, hidden, visible
    'overflowX' => null,        // Horizontal overflow
    'overflowY' => null,        // Vertical overflow

    // Theme Support
    'theme' => 'cinema',        // cinema, admin
    'variant' => 'default',     // default, minimal, thick, invisible

    // Custom Classes
    'class' => '',              // Additional classes
])

@php
    $baseClasses = [];

    // === THEME PRESETS ===
    $themePresets = [
        'cinema' => [
            'default' => [
                'track' => 'cinema-black',
                'thumb' => 'cinema-gold',
                'corner' => 'cinema-black'
            ],
            'minimal' => [
                'track' => 'transparent',
                'thumb' => 'cinema-gold/50',
                'corner' => 'transparent'
            ]
        ],
        'admin' => [
            'default' => [
                'track' => 'gray-100',
                'thumb' => 'gray-400',
                'corner' => 'gray-100'
            ],
            'minimal' => [
                'track' => 'gray-50',
                'thumb' => 'gray-300',
                'corner' => 'gray-50'
            ]
        ]
    ];

    // Apply theme preset if variant exists
    if (isset($themePresets[$theme][$variant])) {
        $preset = $themePresets[$theme][$variant];
        $track = $track ?? $preset['track'];
        $thumb = $thumb ?? $preset['thumb'];
        $corner = $corner ?? $preset['corner'];
    }

    // === SCROLLBAR TYPE ===
    $scrollbarClass = match($scrollbar) {
        'thin' => 'scrollbar-thin',
        'none' => 'scrollbar-none',
        'auto' => 'scrollbar-auto',
        default => ''
    };
    if ($scrollbarClass) $baseClasses[] = $scrollbarClass;

    // Custom scrollbar width
    if ($scrollbarWidth !== null) {
        $baseClasses[] = str_starts_with($scrollbarWidth, 'scrollbar-w-') ? $scrollbarWidth : "scrollbar-w-{$scrollbarWidth}";
    }

    // === OVERFLOW ===
    if ($overflow !== null) {
        $baseClasses[] = match($overflow) {
            'auto' => 'overflow-auto',
            'scroll' => 'overflow-scroll',
            'hidden' => 'overflow-hidden',
            'visible' => 'overflow-visible',
            default => $overflow
        };
    }

    if ($overflowX !== null) {
        $baseClasses[] = match($overflowX) {
            'auto' => 'overflow-x-auto',
            'scroll' => 'overflow-x-scroll',
            'hidden' => 'overflow-x-hidden',
            'visible' => 'overflow-x-visible',
            default => $overflowX
        };
    }

    if ($overflowY !== null) {
        $baseClasses[] = match($overflowY) {
            'auto' => 'overflow-y-auto',
            'scroll' => 'overflow-y-scroll',
            'hidden' => 'overflow-y-hidden',
            'visible' => 'overflow-y-visible',
            default => $overflowY
        };
    }

    // === SIZING ===
    if ($height !== null) {
        $baseClasses[] = str_starts_with($height, 'h-') ? $height : "h-{$height}";
    }

    if ($maxHeight !== null) {
        $baseClasses[] = str_starts_with($maxHeight, 'max-h-') ? $maxHeight : "max-h-{$maxHeight}";
    }

    if ($width !== null) {
        $baseClasses[] = str_starts_with($width, 'w-') ? $width : "w-{$width}";
    }

    if ($maxWidth !== null) {
        $baseClasses[] = str_starts_with($maxWidth, 'max-w-') ? $maxWidth : "max-w-{$maxWidth}";
    }

    // === SCROLLBAR STYLING ===
    if ($track !== null) {
        $baseClasses[] = str_starts_with($track, 'scrollbar-track-') ? $track : "scrollbar-track-{$track}";
    }

    if ($thumb !== null) {
        $baseClasses[] = str_starts_with($thumb, 'scrollbar-thumb-') ? $thumb : "scrollbar-thumb-{$thumb}";
    }

    if ($corner !== null) {
        $baseClasses[] = str_starts_with($corner, 'scrollbar-corner-') ? $corner : "scrollbar-corner-{$corner}";
    }

    // Hover states
    if ($trackHover !== null) {
        $baseClasses[] = $trackHover;
    }

    if ($thumbHover !== null) {
        $baseClasses[] = $thumbHover;
    }

    if ($thumbActive !== null) {
        $baseClasses[] = $thumbActive;
    }

    // Radius
    if ($trackRadius !== null) {
        $baseClasses[] = $trackRadius;
    }

    if ($thumbRadius !== null) {
        $baseClasses[] = $thumbRadius;
    }

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');
@endphp

<div {{ $attributes->twMerge($finalClasses) }}>
    {{ $slot }}
</div>
