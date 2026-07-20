{{--
    Spinner Component - Preline UI Compatible

    Spinner component for loading states
    Based on: https://preline.co/docs/spinners.html

    Safelist Tailwind pour classes dynamiques :
    Sizes: w-4 h-4 w-5 h-5 w-6 h-6 w-8 h-8 w-10 h-10 w-12 h-12
    Colors: text-blue-600 text-green-600 text-red-600 text-yellow-600 text-gray-500
    Animations: animate-spin
--}}
@props([
    // Size
    'size' => 'md',             // xs, sm, md, lg, xl, 2xl

    // Color & Theme
    'color' => 'primary',       // primary, secondary, success, danger, warning, info, gray OR custom CSS classes
    'theme' => 'cinema',        // cinema, admin

    // Type
    'type' => 'default',        // default, dots, bars, pulse

    // Content
    'text' => null,             // Loading text
    'showText' => false,        // Show loading text

    // Custom Classes
    'class' => '',              // Additional classes
])

@php
    $baseClasses = ['inline-block'];

    // === SIZE MAPPING ===
    $sizeMap = [
        'xs' => ['size' => 'w-3 h-3', 'text' => 'text-xs'],
        'sm' => ['size' => 'w-4 h-4', 'text' => 'text-sm'],
        'md' => ['size' => 'w-5 h-5', 'text' => 'text-base'],
        'lg' => ['size' => 'w-6 h-6', 'text' => 'text-lg'],
        'xl' => ['size' => 'w-8 h-8', 'text' => 'text-xl'],
        '2xl' => ['size' => 'w-10 h-10', 'text' => 'text-2xl'],
    ];

    $sizeConfig = $sizeMap[$size] ?? $sizeMap['md'];
    $baseClasses[] = $sizeConfig['size'];

    // === SEMANTIC & DEDICATED COLORS ONLY ===
    $semanticColors = [
        'primary' => [
            'cinema' => 'text-cinema-gold',
            'admin' => 'text-blue-600',
        ],
        'secondary' => [
            'cinema' => 'text-gray-400',
            'admin' => 'text-gray-500',
        ],
        'success' => 'text-green-600',
        'danger' => [
            'cinema' => 'text-cinema-red',
            'admin' => 'text-red-600',
        ],
        'warning' => 'text-yellow-600',
        'info' => 'text-blue-600',
        'gray' => 'text-gray-500',
    ];

    // Get color classes
    if (isset($semanticColors[$color][$theme])) {
        $colorClasses = $semanticColors[$color][$theme];
    } elseif (isset($semanticColors[$color])) {
        $colorClasses = $semanticColors[$color];
    } else {
        // Fallback - use color directly as CSS class
        $colorClasses = $color;
    }

    $baseClasses[] = $colorClasses;

    // === ANIMATION TYPE ===
    if ($type === 'default') {
        $baseClasses[] = 'animate-spin';
    } elseif ($type === 'pulse') {
        $baseClasses[] = 'animate-pulse';
    }

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');

    // === LOADING TEXT ===
    $displayText = $text ?? 'Loading...';
@endphp

@if($type === 'default')
    {{-- Default Spinner (rotating circle) --}}
    <div class="inline-flex items-center {{ $showText ? 'gap-2' : '' }}">
        <svg class="{{ $finalClasses }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        @if($showText)
            <span class="{{ $sizeConfig['text'] }} {{ $colorClasses }}">{{ $displayText }}</span>
        @endif
    </div>

@elseif($type === 'dots')
    {{-- Dots Spinner --}}
    <div class="inline-flex items-center {{ $showText ? 'gap-2' : '' }}">
        <div class="flex space-x-1">
            <div class="{{ $sizeConfig['size'] }} {{ $colorClasses }} rounded-full animate-bounce"></div>
            <div class="{{ $sizeConfig['size'] }} {{ $colorClasses }} rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
            <div class="{{ $sizeConfig['size'] }} {{ $colorClasses }} rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
        </div>
        @if($showText)
            <span class="{{ $sizeConfig['text'] }} {{ $colorClasses }}">{{ $displayText }}</span>
        @endif
    </div>

@elseif($type === 'bars')
    {{-- Bars Spinner --}}
    <div class="inline-flex items-center {{ $showText ? 'gap-2' : '' }}">
        <div class="flex space-x-1">
            @for($i = 1; $i <= 3; $i++)
                <div class="w-1 {{ str_replace('w-', 'h-', $sizeConfig['size']) }} {{ $colorClasses }} animate-pulse" style="animation-delay: {{ ($i - 1) * 0.15 }}s;"></div>
            @endfor
        </div>
        @if($showText)
            <span class="{{ $sizeConfig['text'] }} {{ $colorClasses }}">{{ $displayText }}</span>
        @endif
    </div>

@elseif($type === 'pulse')
    {{-- Pulse Spinner --}}
    <div class="inline-flex items-center {{ $showText ? 'gap-2' : '' }}">
        <div class="{{ $finalClasses }} rounded-full bg-current opacity-20"></div>
        @if($showText)
            <span class="{{ $sizeConfig['text'] }} {{ $colorClasses }}">{{ $displayText }}</span>
        @endif
    </div>

@else
    {{-- Custom content via slot --}}
    <div class="inline-flex items-center {{ $showText ? 'gap-2' : '' }}">
        <div class="{{ $finalClasses }}">
            {{ $slot }}
        </div>
        @if($showText)
            <span class="{{ $sizeConfig['text'] }} {{ $colorClasses }}">{{ $displayText }}</span>
        @endif
    </div>
@endif