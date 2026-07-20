{{--
    Badge Component - Preline UI Compatible

    Badge component following Preline pattern
    Based on: https://preline.co/docs/badge.html

    Safelist Tailwind pour classes dynamiques :
    Sizes: text-xs text-sm text-base px-1 px-2 px-3 py-0.5 py-1 py-1.5
    Colors: bg-blue-100 bg-green-100 bg-red-100 bg-yellow-100 text-blue-800 text-green-800
    Borders: border border-blue-200 border-green-200 border-red-200
    Animations: animate-ping
--}}
@props([
    // Content
    'text' => null,             // Badge text (alternative to slot)
    'icon' => null,             // Icon component/class
    'iconPosition' => 'left',   // left, right

    // Size & Shape
    'size' => 'sm',             // xs, sm, md, lg
    'rounded' => 'rounded-md',  // rounded-none, rounded-sm, rounded-md, rounded-lg, rounded-full, rounded-xl

    // Color & Theme
    'color' => 'primary',       // primary, secondary, success, danger, warning, info, gray OR custom CSS classes
    'variant' => 'solid',       // solid, soft, outlined, white
    'theme' => 'cinema',        // cinema, admin

    // Features
    'removable' => false,       // Show remove button
    'removeIcon' => 'heroicon-o-x-mark', // Custom remove icon
    'indicator' => false,       // Show indicator dot
    'indicatorColor' => null,   // Custom indicator color
    'ping' => false,            // Animate indicator with ping
    'positioned' => false,      // Positioned badge (absolute)
    'position' => 'top-right',  // top-left, top-right, bottom-left, bottom-right

    // Avatar Support
    'withAvatar' => false,      // Show with avatar
    'avatarSrc' => null,        // Avatar image src
    'avatarName' => null,       // Avatar name for initials

    // Button Integration
    'withButton' => false,      // Integrate with button styling
    'buttonText' => 'Button',   // Button text when withButton=true
    'buttonSize' => 'sm',       // Button size when withButton=true

    // Interactive
    'clickable' => false,       // Make badge clickable
    'href' => null,             // Link href
    'target' => null,           // Link target

    // Custom Classes
    'class' => '',              // Additional classes
])

@php
    $baseClasses = ['inline-flex', 'items-center', 'font-medium'];
    $removeClasses = ['ml-1', 'inline-flex', 'flex-shrink-0'];

    // === SIZE MAPPING ===
    $sizeMap = [
        'xs' => ['padding' => 'px-1 py-0.5', 'text' => 'text-xs', 'gap' => 'gap-1'],
        'sm' => ['padding' => 'px-2 py-1', 'text' => 'text-xs', 'gap' => 'gap-1'],
        'md' => ['padding' => 'px-2.5 py-1.5', 'text' => 'text-sm', 'gap' => 'gap-1.5'],
        'lg' => ['padding' => 'px-3 py-2', 'text' => 'text-base', 'gap' => 'gap-2'],
    ];

    $sizeConfig = $sizeMap[$size] ?? $sizeMap['sm'];
    $baseClasses[] = $sizeConfig['padding'];
    $baseClasses[] = $sizeConfig['text'];
    $baseClasses[] = $sizeConfig['gap'];

    // === SEMANTIC & DEDICATED COLORS ONLY ===
    $semanticColors = [
        'primary' => [
            'cinema' => [
                'solid' => 'bg-cinema-gold text-cinema-black',
                'soft' => 'bg-cinema-gold/20 text-cinema-gold',
                'outlined' => 'border border-cinema-gold text-cinema-gold bg-transparent',
                'white' => 'bg-white text-cinema-gold border border-cinema-gold/20',
            ],
            'admin' => [
                'solid' => 'bg-blue-600 text-white',
                'soft' => 'bg-blue-100 text-blue-800',
                'outlined' => 'border border-blue-200 text-blue-600 bg-transparent',
                'white' => 'bg-white text-blue-600 border border-blue-200',
            ],
        ],
        'secondary' => [
            'cinema' => [
                'solid' => 'bg-gray-500 text-white',
                'soft' => 'bg-gray-500/20 text-gray-400',
                'outlined' => 'border border-gray-400 text-gray-400 bg-transparent',
                'white' => 'bg-white text-gray-600 border border-gray-200',
            ],
            'admin' => [
                'solid' => 'bg-gray-500 text-white',
                'soft' => 'bg-gray-100 text-gray-800',
                'outlined' => 'border border-gray-200 text-gray-600 bg-transparent',
                'white' => 'bg-white text-gray-600 border border-gray-200',
            ],
        ],
        'success' => [
            'solid' => 'bg-green-600 text-white',
            'soft' => 'bg-green-100 text-green-800',
            'outlined' => 'border border-green-200 text-green-600 bg-transparent',
            'white' => 'bg-white text-green-600 border border-green-200',
        ],
        'danger' => [
            'cinema' => [
                'solid' => 'bg-cinema-red text-white',
                'soft' => 'bg-cinema-red/20 text-cinema-red',
                'outlined' => 'border border-cinema-red text-cinema-red bg-transparent',
                'white' => 'bg-white text-cinema-red border border-cinema-red/20',
            ],
            'admin' => [
                'solid' => 'bg-red-600 text-white',
                'soft' => 'bg-red-100 text-red-800',
                'outlined' => 'border border-red-200 text-red-600 bg-transparent',
                'white' => 'bg-white text-red-600 border border-red-200',
            ],
        ],
        'warning' => [
            'solid' => 'bg-yellow-500 text-white',
            'soft' => 'bg-yellow-100 text-yellow-800',
            'outlined' => 'border border-yellow-200 text-yellow-600 bg-transparent',
            'white' => 'bg-white text-yellow-600 border border-yellow-200',
        ],
        'info' => [
            'solid' => 'bg-blue-600 text-white',
            'soft' => 'bg-blue-100 text-blue-800',
            'outlined' => 'border border-blue-200 text-blue-600 bg-transparent',
            'white' => 'bg-white text-blue-600 border border-blue-200',
        ],
        'gray' => [
            'solid' => 'bg-gray-500 text-white',
            'soft' => 'bg-gray-100 text-gray-800',
            'outlined' => 'border border-gray-200 text-gray-600 bg-transparent',
            'white' => 'bg-white text-gray-600 border border-gray-200',
        ],
    ];

    // Get color classes
    if (isset($semanticColors[$color][$theme][$variant])) {
        $colorClasses = $semanticColors[$color][$theme][$variant];
    } elseif (isset($semanticColors[$color][$variant])) {
        $colorClasses = $semanticColors[$color][$variant];
    } else {
        // Fallback - use color directly as CSS class
        $colorClasses = $color;
    }

    $baseClasses[] = $colorClasses;

    // === SHAPE ===
    $baseClasses[] = $rounded;

    // === INTERACTIVE ===
    if ($clickable || $href) {
        $baseClasses[] = 'cursor-pointer hover:opacity-80 transition-opacity duration-200';
    }

    // === POSITIONED ===
    if ($positioned) {
        $baseClasses[] = 'absolute';
        $positionClasses = match($position) {
            'top-left' => 'top-0 left-0 -translate-y-1/2 -translate-x-1/2',
            'top-right' => 'top-0 right-0 -translate-y-1/2 translate-x-1/2',
            'bottom-left' => 'bottom-0 left-0 translate-y-1/2 -translate-x-1/2',
            'bottom-right' => 'bottom-0 right-0 translate-y-1/2 translate-x-1/2',
            default => $position
        };
        $baseClasses[] = $positionClasses;
    }

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');

    // === INDICATOR CONFIG ===
    $indicatorConfig = null;
    if ($indicator) {
        $indicatorSize = match($size) {
            'xs' => 'w-1.5 h-1.5',
            'sm' => 'w-2 h-2',
            'md' => 'w-2.5 h-2.5',
            'lg' => 'w-3 h-3',
            default => 'w-2 h-2'
        };

        $indicatorColorClass = $indicatorColor ?? match($color) {
            'success' => 'bg-green-500',
            'danger', 'cinema-red' => 'bg-red-500',
            'warning' => 'bg-yellow-500',
            'info' => 'bg-blue-500',
            'cinema-gold' => 'bg-cinema-gold',
            default => 'bg-blue-500'
        };

        $indicatorClasses = ['rounded-full', $indicatorSize, $indicatorColorClass];
        if ($ping) {
            $indicatorClasses[] = 'animate-ping';
        }

        $indicatorConfig = [
            'classes' => implode(' ', $indicatorClasses)
        ];
    }

    // === WRAPPER ELEMENT ===
    $element = $href ? 'a' : 'span';
    $elementAttributes = [];
    if ($href) {
        $elementAttributes['href'] = $href;
        if ($target) $elementAttributes['target'] = $target;
    }
@endphp

@if($withButton)
    <div class="relative inline-flex">
        <button type="button" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            {{ $buttonText ?? 'Button' }}
        </button>
        <span class="{{ $finalClasses }} absolute -top-2 -right-2">
            {{ $text ?? $slot }}
        </span>
    </div>
@else
    <{{ $element }} {{ $attributes->merge($elementAttributes)->twMerge($finalClasses) }}>
        {{-- Avatar --}}
        @if($withAvatar)
            @if($avatarSrc || $avatarName)
                <x-avatar
                    :src="$avatarSrc"
                    :name="$avatarName"
                    size="xs"
                    :theme="$theme"
                />
            @else
                {{ $avatar ?? '' }}
            @endif
        @endif

        {{-- Indicator --}}
        @if($indicatorConfig)
            <span class="{{ $indicatorConfig['classes'] }}"></span>
        @endif

        {{-- Icon Left --}}
        @if($icon && $iconPosition === 'left')
            <x-icon-wrap size="3" color="currentColor">
                @svg($icon, 'w-3 h-3')
            </x-icon-wrap>
        @endif

        {{-- Content --}}
        @if($text)
            {{ $text }}
        @else
            {{ $slot }}
        @endif

        {{-- Icon Right --}}
        @if($icon && $iconPosition === 'right')
            <x-icon-wrap size="3" color="currentColor">
                @svg($icon, 'w-3 h-3')
            </x-icon-wrap>
        @endif

        {{-- Remove Button --}}
        @if($removable)
            <button type="button" class="{{ implode(' ', $removeClasses) }} hover:opacity-70 transition-opacity" onclick="this.closest('{{ $element }}').remove()">
                <x-icon-wrap size="3" color="currentColor">
                    @svg($removeIcon, 'w-3 h-3')
                </x-icon-wrap>
            </button>
        @endif
    </{{ $element }}>
@endif