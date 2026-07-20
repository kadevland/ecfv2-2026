{{--
    Alert Component - Preline UI Compatible

    Alert component for notifications and messages
    Based on: https://preline.co/docs/alerts.html

    Safelist Tailwind pour classes dynamiques :
    Colors: bg-blue-50 bg-green-50 bg-yellow-50 bg-red-50 bg-gray-50 bg-cinema-gold bg-cinema-red
    Text colors: text-blue-800 text-green-800 text-yellow-800 text-red-800 text-gray-800 text-cinema-black text-white
    Border colors: border-blue-200 border-green-200 border-yellow-200 border-red-200 border-gray-200 border-cinema-gold border-cinema-red
--}}
@props([
    // Alert Type & Styling
    'type' => 'info',           // info, success, warning, error, cinema, admin
    'variant' => 'default',     // default, bordered, soft, solid, minimal
    'theme' => 'cinema',        // cinema, admin

    // Content
    'title' => null,            // Alert title
    'message' => null,          // Alert message (alternative to slot)
    'icon' => null,             // Custom icon (Blade UI Kit name or null for default)
    'showIcon' => true,         // Show/hide icon

    // Dismissible
    'dismissible' => false,     // Make alert dismissible
    'dismissText' => '×',       // Text for dismiss button
    'dismissIcon' => null,      // Custom dismiss icon

    // Layout
    'size' => 'default',        // sm, default, lg
    'rounded' => null,          // Rounded corners
    'shadow' => null,           // Shadow styling

    // Custom Classes
    'class' => '',              // Additional classes
])

@php
    $baseClasses = ['flex', 'items-start', 'p-4', 'space-x-3'];
    $iconClasses = ['flex-shrink-0', 'w-5', 'h-5', 'mt-0.5'];
    $contentClasses = ['flex-1', 'min-w-0'];
    $dismissClasses = ['ml-auto', 'pl-3', 'flex-shrink-0'];

    // === THEME CONFIGURATIONS ===
    $themeConfigs = [
        'cinema' => [
            'info' => [
                'default' => 'bg-blue-900/20 text-blue-200 border-blue-700',
                'bordered' => 'bg-transparent border-2 border-blue-500 text-blue-400',
                'soft' => 'bg-blue-500/10 text-blue-300',
                'solid' => 'bg-blue-600 text-white',
                'minimal' => 'bg-transparent text-blue-400',
                'icon' => 'heroicon-o-information-circle'
            ],
            'success' => [
                'default' => 'bg-green-900/20 text-green-200 border-green-700',
                'bordered' => 'bg-transparent border-2 border-green-500 text-green-400',
                'soft' => 'bg-green-500/10 text-green-300',
                'solid' => 'bg-green-600 text-white',
                'minimal' => 'bg-transparent text-green-400',
                'icon' => 'heroicon-o-check-circle'
            ],
            'warning' => [
                'default' => 'bg-cinema-gold/20 text-cinema-gold border-cinema-gold/50',
                'bordered' => 'bg-transparent border-2 border-cinema-gold text-cinema-gold',
                'soft' => 'bg-cinema-gold/10 text-cinema-gold',
                'solid' => 'bg-cinema-gold text-cinema-black',
                'minimal' => 'bg-transparent text-cinema-gold',
                'icon' => 'heroicon-o-exclamation-triangle'
            ],
            'error' => [
                'default' => 'bg-cinema-red/20 text-cinema-red-light border-cinema-red/50',
                'bordered' => 'bg-transparent border-2 border-cinema-red text-cinema-red-light',
                'soft' => 'bg-cinema-red/10 text-cinema-red-light',
                'solid' => 'bg-cinema-red text-white',
                'minimal' => 'bg-transparent text-cinema-red-light',
                'icon' => 'heroicon-o-x-circle'
            ]
        ],
        'admin' => [
            'info' => [
                'default' => 'bg-blue-50 text-blue-800 border-blue-200',
                'bordered' => 'bg-white border-2 border-blue-300 text-blue-700',
                'soft' => 'bg-blue-25 text-blue-700',
                'solid' => 'bg-blue-600 text-white',
                'minimal' => 'bg-transparent text-blue-600',
                'icon' => 'heroicon-o-information-circle'
            ],
            'success' => [
                'default' => 'bg-green-50 text-green-800 border-green-200',
                'bordered' => 'bg-white border-2 border-green-300 text-green-700',
                'soft' => 'bg-green-25 text-green-700',
                'solid' => 'bg-green-600 text-white',
                'minimal' => 'bg-transparent text-green-600',
                'icon' => 'heroicon-o-check-circle'
            ],
            'warning' => [
                'default' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                'bordered' => 'bg-white border-2 border-yellow-300 text-yellow-700',
                'soft' => 'bg-yellow-25 text-yellow-700',
                'solid' => 'bg-yellow-500 text-white',
                'minimal' => 'bg-transparent text-yellow-600',
                'icon' => 'heroicon-o-exclamation-triangle'
            ],
            'error' => [
                'default' => 'bg-red-50 text-red-800 border-red-200',
                'bordered' => 'bg-white border-2 border-red-300 text-red-700',
                'soft' => 'bg-red-25 text-red-700',
                'solid' => 'bg-red-600 text-white',
                'minimal' => 'bg-transparent text-red-600',
                'icon' => 'heroicon-o-x-circle'
            ]
        ]
    ];

    // Get theme configuration
    $config = $themeConfigs[$theme][$type] ?? $themeConfigs['admin']['info'];
    $alertClasses = $config[$variant] ?? $config['default'];
    $defaultIcon = $icon ?? $config['icon'];

    // Apply styling
    $baseClasses[] = $alertClasses;

    // === SIZE VARIATIONS ===
    if ($size === 'sm') {
        $baseClasses[] = 'p-3 text-sm';
        $iconClasses = ['flex-shrink-0', 'w-4', 'h-4', 'mt-0.5'];
    } elseif ($size === 'lg') {
        $baseClasses[] = 'p-6 text-lg';
        $iconClasses = ['flex-shrink-0', 'w-6', 'h-6', 'mt-0.5'];
    }

    // === ROUNDED ===
    if ($rounded !== null) {
        $roundedClass = match($rounded) {
            'none' => 'rounded-none',
            'sm' => 'rounded-sm',
            'md' => 'rounded-md',
            'lg' => 'rounded-lg',
            'xl' => 'rounded-xl',
            'full' => 'rounded-full',
            default => $rounded
        };
        $baseClasses[] = $roundedClass;
    } else {
        $baseClasses[] = 'rounded-lg'; // Default
    }

    // === SHADOW ===
    if ($shadow !== null) {
        $baseClasses[] = str_starts_with($shadow, 'shadow') ? $shadow : "shadow-{$shadow}";
    }

    // === BORDER FOR VARIANTS ===
    if (in_array($variant, ['default', 'bordered', 'soft'])) {
        $baseClasses[] = 'border';
    }

    // === DISMISSIBLE STYLING ===
    if ($dismissible) {
        $dismissClasses[] = 'cursor-pointer hover:opacity-70 transition-opacity';
    }

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');

    $finalIconClasses = implode(' ', $iconClasses);
    $finalContentClasses = implode(' ', $contentClasses);
    $finalDismissClasses = implode(' ', $dismissClasses);

    // Generate unique ID for dismissible alerts
    $alertId = 'alert-' . uniqid();

    // Default Preline icons (SVG) if no Blade UI Kit icon specified
    $prelineIcons = [
        'info' => '<svg class="' . $finalIconClasses . '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>',
        'success' => '<svg class="' . $finalIconClasses . '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22,4 12,14.01 9,11.01"></polyline></svg>',
        'warning' => '<svg class="' . $finalIconClasses . '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><path d="M12 9v4"></path><path d="m12 17 .01 0"></path></svg>',
        'error' => '<svg class="' . $finalIconClasses . '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m15 9-6 6"></path><path d="m9 9 6 6"></path></svg>'
    ];
@endphp

<div {{ $attributes->twMerge($finalClasses) }} role="alert" @if($dismissible) id="{{ $alertId }}" @endif>
    @if($showIcon)
        <div class="{{ $finalIconClasses }}">
            @if($icon && str_starts_with($icon, 'heroicon'))
                {{-- Blade UI Kit Icon --}}
                @svg($icon, $finalIconClasses)
            @else
                {{-- Default Preline SVG Icon --}}
                {!! $prelineIcons[$type] ?? $prelineIcons['info'] !!}
            @endif
        </div>
    @endif

    <div class="{{ $finalContentClasses }}">
        @if($title)
            <h3 class="font-semibold">{{ $title }}</h3>
        @endif

        @if($message)
            <div class="@if($title) mt-1 @endif">
                {{ $message }}
            </div>
        @elseif($slot->isNotEmpty())
            <div class="@if($title) mt-1 @endif">
                {{ $slot }}
            </div>
        @endif
    </div>

    @if($dismissible)
        <div class="{{ $finalDismissClasses }}">
            <button type="button" data-hs-remove-element="#{{ $alertId }}" class="inline-flex rounded-md p-1.5 hover:bg-black/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-transparent focus:ring-white/20" aria-label="Close">
                @if($dismissIcon)
                    @svg($dismissIcon, 'w-4 h-4')
                @else
                    <span class="text-xl leading-none">{{ $dismissText }}</span>
                @endif
            </button>
        </div>
    @endif
</div>
