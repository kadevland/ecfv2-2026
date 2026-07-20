{{--
    Back to Top Page Component - Preline UI Compatible

    Floating back to top button with progress indicator
    Uses scroll-to component internally
    Based on: https://preline.co/plugins/html/scrollspy.html

    Usage:
    <x-back-top-page />
    <x-back-top-page theme="cinema" position="bottom-left" :show-progress="true" />

    Safelist Tailwind pour classes dynamiques :
    Positions: bottom-4 bottom-6 bottom-8 right-4 right-6 right-8 left-4 left-6 left-8
    Themes: bg-cinema-dark bg-white border-cinema-gold border-gray-200
    Progress: stroke-cinema-gold stroke-blue-600
--}}
@props([
    // Visual Configuration
    'theme' => 'admin',                 // admin | cinema
    'size' => 'md',                     // sm | md | lg
    'position' => 'bottom-right',       // bottom-right | bottom-left | bottom-center

    // Behavior
    'showProgress' => true,             // Show reading progress circle
    'autoHide' => true,                 // Hide when at top of page
    'threshold' => 300,                 // Pixels scrolled before showing

    // Visual Features
    'icon' => null,                     // Custom icon (HTML or class)
    'tooltip' => true,                  // Show tooltip on hover
    'tooltipText' => 'Retour en haut',  // Tooltip text
    'pulse' => false,                   // Pulse animation

    // Accessibility
    'ariaLabel' => 'Retourner en haut de la page',

    // Custom Classes
    'class' => '',                      // Additional classes
])

@php
    // === THEME MAPPING ===
    $themeConfig = [
        'cinema' => [
            'bg' => 'bg-gray-800/95 backdrop-blur-sm',
            'border' => 'border border-cinema-gold/40',
            'text' => 'text-cinema-gold',
            'hover' => 'hover:bg-gray-700 hover:border-cinema-gold hover:shadow-lg hover:shadow-cinema-gold/25',
            'progress' => 'stroke-cinema-gold',
            'tooltip' => 'bg-gray-800 border-cinema-gold/40 text-cinema-gold',
        ],
        'admin' => [
            'bg' => 'bg-white/90 backdrop-blur-sm',
            'border' => 'border border-gray-200',
            'text' => 'text-gray-600',
            'hover' => 'hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 hover:shadow-lg',
            'progress' => 'stroke-blue-600',
            'tooltip' => 'bg-gray-900 text-white',
        ],
    ];

    $currentTheme = $themeConfig[$theme] ?? $themeConfig['admin'];

    // === SIZE MAPPING ===
    $sizeConfig = [
        'sm' => [
            'button' => 'w-10 h-10',
            'icon' => 'w-4 h-4',
            'progress' => 'w-10 h-10',
            'text' => 'text-sm',
        ],
        'md' => [
            'button' => 'w-12 h-12',
            'icon' => 'w-5 h-5',
            'progress' => 'w-12 h-12',
            'text' => 'text-base',
        ],
        'lg' => [
            'button' => 'w-14 h-14',
            'icon' => 'w-6 h-6',
            'progress' => 'w-14 h-14',
            'text' => 'text-lg',
        ],
    ];

    $currentSize = $sizeConfig[$size] ?? $sizeConfig['md'];

    // === POSITION MAPPING ===
    $positionConfig = [
        'bottom-right' => 'bottom-6 right-6',
        'bottom-left' => 'bottom-6 left-6',
        'bottom-center' => 'bottom-6 left-1/2 transform -translate-x-1/2',
    ];

    $positionClasses = $positionConfig[$position] ?? $positionConfig['bottom-right'];

    // === BUTTON CLASSES ===
    $buttonClasses = collect([
        'back-top-button', // CSS class définie dans scroll.css
        'fixed',
        'z-50',
        $positionClasses,
        $currentSize['button'],
        'rounded-full',
        $currentTheme['bg'],
        $currentTheme['border'],
        $currentTheme['text'],
        $currentTheme['hover'],
        'shadow-lg',
        'cursor-pointer',
        'flex',
        'items-center',
        'justify-center',
        'group',
        $pulse ? 'animate-pulse' : '',
        $class
    ])->filter()->implode(' ');

    // === PROGRESS CIRCLE SIZE ===
    $circleSize = match($size) {
        'sm' => 40,
        'md' => 48,
        'lg' => 56,
        default => 48
    };

    $circleRadius = ($circleSize - 8) / 2;
    $circleCircumference = 2 * pi() * $circleRadius;

    // === UNIQUE ID ===
    $uniqueId = 'back-top-' . uniqid();
@endphp

{{-- Back to Top Button --}}
<div id="{{ $uniqueId }}"
     class="{{ $buttonClasses }}"
     data-back-top-threshold="{{ $threshold }}"
     data-back-top-auto-hide="{{ $autoHide ? 'true' : 'false' }}"
     style="display: {{ $autoHide ? 'none' : 'flex' }};"
     @if($tooltip)
     data-tooltip="{{ $tooltipText }}"
     @endif>

    {{-- Progress Circle (if enabled) --}}
    @if($showProgress)
    <svg class="absolute inset-0 {{ $currentSize['progress'] }} transform -rotate-90"
         viewBox="0 0 {{ $circleSize }} {{ $circleSize }}">
        {{-- Background Circle --}}
        <circle cx="{{ $circleSize / 2 }}" cy="{{ $circleSize / 2 }}" r="{{ $circleRadius }}"
                stroke="currentColor" stroke-width="2" fill="none" opacity="0.2"/>
        {{-- Progress Circle --}}
        <circle id="{{ $uniqueId }}-progress"
                cx="{{ $circleSize / 2 }}" cy="{{ $circleSize / 2 }}" r="{{ $circleRadius }}"
                stroke="currentColor" stroke-width="2" fill="none"
                class="{{ $currentTheme['progress'] }} progress-circle"
                stroke-dasharray="{{ $circleCircumference }}"
                stroke-dashoffset="{{ $circleCircumference }}"
                stroke-linecap="round"/>
    </svg>
    @endif

    {{-- Scroll To Top Button --}}
    <button type="button"
            onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
            aria-label="{{ $ariaLabel }}"
            class="relative z-10 flex items-center justify-center {{ $currentSize['button'] }} rounded-full">

        {{-- Icon --}}
        @if($icon)
            @if(is_string($icon))
                <div class="{{ $icon }} {{ $currentSize['icon'] }}"></div>
            @else
                {{ $icon }}
            @endif
        @else
            {{-- Default Up Arrow Icon --}}
            <svg class="{{ $currentSize['icon'] }} transition-transform group-hover:-translate-y-0.5"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 10l7-7m0 0l7 7m-7-7v18"/>
            </svg>
        @endif

    </button>

    {{-- Tooltip --}}
    @if($tooltip)
    <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 px-2 py-1 {{ $currentTheme['tooltip'] }} rounded text-xs whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-20">
        {{ $tooltipText }}
        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-current"></div>
    </div>
    @endif
</div>

{{-- JavaScript is handled by resources/js/components/scrollto.js --}}