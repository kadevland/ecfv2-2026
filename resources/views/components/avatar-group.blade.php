{{--
    Avatar Group Component - Preline UI Compatible

    Avatar group component for displaying multiple avatars
    Based on: https://preline.co/docs/avatar-group.html

    Safelist Tailwind pour classes dynamiques :
    Spacing: -space-x-1 -space-x-2 -space-x-3 -space-x-4 -space-x-6 space-x-1 space-x-2 space-x-4
    Gaps: gap-1 gap-2 gap-3 gap-4 gap-6 gap-8
    Grid cols: grid-cols-2 grid-cols-3 grid-cols-4 grid-cols-5 grid-cols-6
--}}
@props([
    // Layout Type
    'layout' => 'stack',        // stack, grid, list
    'spacing' => '-space-x-2',  // spacing between avatars for stack layout
    'gap' => 'gap-2',           // gap for grid/list layout
    'cols' => '3',              // grid columns for grid layout

    // Icon Style (for icons in slot)
    'icon-style' => null,       // CSS classes for icons in slot

    // Custom Classes
    'class' => '',              // Additional classes
])

@php
    $baseClasses = ['flex', 'items-center'];

    // === LAYOUT CONFIGURATION ===
    if ($layout === 'stack') {
        $baseClasses[] = $spacing;
    } elseif ($layout === 'grid') {
        $baseClasses = ['grid', "grid-cols-{$cols}", $gap];
    } elseif ($layout === 'list') {
        $baseClasses[] = $gap;
        $baseClasses[] = 'flex-wrap';
    }

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');
@endphp

<div {{ $attributes->twMerge($finalClasses) }}>
    {{ $slot }}
</div>

