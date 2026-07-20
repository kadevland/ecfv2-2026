{{--
    Button Component - Preline UI Compatible

    Button component following Preline pattern
    Based on: https://preline.co/docs/buttons.html

    Safelist Tailwind pour classes dynamiques :
    Sizes: text-xs text-sm text-base text-lg px-2 px-3 px-4 px-6 py-1 py-2 py-3
    Colors: bg-blue-600 bg-green-600 bg-red-600 border-blue-600 border-green-600 text-blue-600
    States: hover:bg-blue-700 focus:ring-blue-500 disabled:opacity-50
--}}
@props([
    // Content
    'text' => null,             // Button text (alternative to slot)
    'icon' => null,             // Icon component/class
    'iconPosition' => 'left',   // left, right

    // Size
    'size' => 'md',             // xs, sm, md, lg, xl
    'iconSize' => 'xs',             // xs, sm, md, lg, xl

    // Color & Theme
    'color' => 'primary',       // primary, secondary, success, danger, warning, info, gray OR custom CSS classes
    'variant' => 'solid',       // solid, outlined, ghost, soft, white, link
    'theme' => 'cinema',        // cinema, admin

    // Type & Behavior
    'type' => 'button',         // button, submit, reset
    'href' => null,             // Link href (creates <a> instead of <button>)
    'target' => null,           // Link target
    'disabled' => false,        // Disabled state

    // Loading State
    'loading' => false,         // Show loading spinner
    'loadingText' => 'Loading...', // Text during loading

    // Avatar Support
    'withAvatar' => false,      // Show with avatar
    'avatarSrc' => null,        // Avatar image src
    'avatarName' => null,       // Avatar name for initials
    'avatarSize' => 'xs',       // Avatar size

    // Custom Classes
    'class' => '',              // Additional classes
])

@php
    $baseClasses = ['inline-flex', 'items-center', 'justify-center', 'font-medium', 'transition-all', 'duration-200'];

    // === SIZE MAPPING ===
    $sizeMap = [
        'xs' => ['padding' => 'px-2 py-1', 'text' => 'text-xs', 'gap' => 'gap-1'],
        'sm' => ['padding' => 'px-3 py-2', 'text' => 'text-sm', 'gap' => 'gap-1.5'],
        'md' => ['padding' => 'px-4 py-2.5', 'text' => 'text-sm', 'gap' => 'gap-2'],
        'lg' => ['padding' => 'px-6 py-3', 'text' => 'text-base', 'gap' => 'gap-2'],
        'xl' => ['padding' => 'px-8 py-4', 'text' => 'text-lg', 'gap' => 'gap-2.5'],
    ];

    $sizeConfig = $sizeMap[$size] ?? $sizeMap['md'];
    $baseClasses[] = $sizeConfig['padding'];
    $baseClasses[] = $sizeConfig['text'];
    $baseClasses[] = $sizeConfig['gap'];

    // === SEMANTIC & DEDICATED COLORS ONLY ===
    $semanticColors = [
        'primary' => [
            'cinema' => [
                'solid' => 'bg-cinema-gold text-cinema-black hover:bg-cinema-gold/90 focus:ring-cinema-gold/50',
                'outlined' => 'border border-cinema-gold text-cinema-gold hover:bg-cinema-gold hover:text-cinema-black focus:ring-cinema-gold/50',
                'ghost' => 'text-cinema-gold hover:bg-cinema-gold/10 focus:ring-cinema-gold/50',
                'soft' => 'bg-cinema-gold/20 text-cinema-gold hover:bg-cinema-gold/30 focus:ring-cinema-gold/50',
                'white' => 'bg-white text-cinema-gold border border-cinema-gold/20 hover:bg-cinema-gold/5 focus:ring-cinema-gold/50',
                'link' => 'text-cinema-gold hover:underline focus:ring-cinema-gold/50',
            ],
            'admin' => [
                'solid' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
                'outlined' => 'border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white focus:ring-blue-500',
                'ghost' => 'text-blue-600 hover:bg-blue-100 focus:ring-blue-500',
                'soft' => 'bg-blue-100 text-blue-800 hover:bg-blue-200 focus:ring-blue-500',
                'white' => 'bg-white text-blue-600 border border-blue-200 hover:bg-blue-50 focus:ring-blue-500',
                'link' => 'text-blue-600 hover:underline focus:ring-blue-500',
            ],
        ],
        'secondary' => [
            'cinema' => [
                'solid' => 'bg-gray-500 text-white hover:bg-gray-600 focus:ring-gray-500',
                'outlined' => 'border border-gray-400 text-gray-400 hover:bg-gray-400 hover:text-white focus:ring-gray-500',
                'ghost' => 'text-gray-400 hover:bg-gray-500/10 focus:ring-gray-500',
                'soft' => 'bg-gray-500/20 text-gray-400 hover:bg-gray-500/30 focus:ring-gray-500',
                'white' => 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 focus:ring-gray-500',
                'link' => 'text-gray-400 hover:underline focus:ring-gray-500',
            ],
            'admin' => [
                'solid' => 'bg-gray-500 text-white hover:bg-gray-600 focus:ring-gray-500',
                'outlined' => 'border border-gray-500 text-gray-600 hover:bg-gray-500 hover:text-white focus:ring-gray-500',
                'ghost' => 'text-gray-600 hover:bg-gray-100 focus:ring-gray-500',
                'soft' => 'bg-gray-100 text-gray-800 hover:bg-gray-200 focus:ring-gray-500',
                'white' => 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 focus:ring-gray-500',
                'link' => 'text-gray-600 hover:underline focus:ring-gray-500',
            ],
        ],
        'success' => [
            'solid' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
            'outlined' => 'border border-green-600 text-green-600 hover:bg-green-600 hover:text-white focus:ring-green-500',
            'ghost' => 'text-green-600 hover:bg-green-100 focus:ring-green-500',
            'soft' => 'bg-green-100 text-green-800 hover:bg-green-200 focus:ring-green-500',
            'white' => 'bg-white text-green-600 border border-green-200 hover:bg-green-50 focus:ring-green-500',
            'link' => 'text-green-600 hover:underline focus:ring-green-500',
        ],
        'danger' => [
            'cinema' => [
                'solid' => 'bg-cinema-red text-white hover:bg-cinema-red/90 focus:ring-cinema-red/50',
                'outlined' => 'border border-cinema-red text-cinema-red hover:bg-cinema-red hover:text-white focus:ring-cinema-red/50',
                'ghost' => 'text-cinema-red hover:bg-cinema-red/10 focus:ring-cinema-red/50',
                'soft' => 'bg-cinema-red/20 text-cinema-red hover:bg-cinema-red/30 focus:ring-cinema-red/50',
                'white' => 'bg-white text-cinema-red border border-cinema-red/20 hover:bg-cinema-red/5 focus:ring-cinema-red/50',
                'link' => 'text-cinema-red hover:underline focus:ring-cinema-red/50',
            ],
            'admin' => [
                'solid' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
                'outlined' => 'border border-red-600 text-red-600 hover:bg-red-600 hover:text-white focus:ring-red-500',
                'ghost' => 'text-red-600 hover:bg-red-100 focus:ring-red-500',
                'soft' => 'bg-red-100 text-red-800 hover:bg-red-200 focus:ring-red-500',
                'white' => 'bg-white text-red-600 border border-red-200 hover:bg-red-50 focus:ring-red-500',
                'link' => 'text-red-600 hover:underline focus:ring-red-500',
            ],
        ],
        'warning' => [
            'solid' => 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500',
            'outlined' => 'border border-yellow-500 text-yellow-600 hover:bg-yellow-500 hover:text-white focus:ring-yellow-500',
            'ghost' => 'text-yellow-600 hover:bg-yellow-100 focus:ring-yellow-500',
            'soft' => 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200 focus:ring-yellow-500',
            'white' => 'bg-white text-yellow-600 border border-yellow-200 hover:bg-yellow-50 focus:ring-yellow-500',
            'link' => 'text-yellow-600 hover:underline focus:ring-yellow-500',
        ],
        'info' => [
            'solid' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
            'outlined' => 'border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white focus:ring-blue-500',
            'ghost' => 'text-blue-600 hover:bg-blue-100 focus:ring-blue-500',
            'soft' => 'bg-blue-100 text-blue-800 hover:bg-blue-200 focus:ring-blue-500',
            'white' => 'bg-white text-blue-600 border border-blue-200 hover:bg-blue-50 focus:ring-blue-500',
            'link' => 'text-blue-600 hover:underline focus:ring-blue-500',
        ],
        'gray' => [
            'solid' => 'bg-gray-500 text-white hover:bg-gray-600 focus:ring-gray-500',
            'outlined' => 'border border-gray-500 text-gray-600 hover:bg-gray-500 hover:text-white focus:ring-gray-500',
            'ghost' => 'text-gray-600 hover:bg-gray-100 focus:ring-gray-500',
            'soft' => 'bg-gray-100 text-gray-800 hover:bg-gray-200 focus:ring-gray-500',
            'white' => 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 focus:ring-gray-500',
            'link' => 'text-gray-600 hover:underline focus:ring-gray-500',
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
    if ($variant === 'link') {
        $baseClasses[] = 'underline-offset-4';
    } else {
        $baseClasses[] = 'rounded-lg';
        $baseClasses[] = 'focus:outline-none focus:ring-2 focus:ring-offset-2';
    }

    // === DISABLED STATE ===
    if ($disabled || $loading) {
        $baseClasses[] = 'disabled:opacity-50 disabled:cursor-not-allowed';
    }

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');

    // === WRAPPER ELEMENT ===
    $element = $href ? 'a' : 'button';
    $elementAttributes = [];

    if ($href) {
        $elementAttributes['href'] = $href;
        if ($target) $elementAttributes['target'] = $target;
    } else {
        $elementAttributes['type'] = $type;
        if ($disabled || $loading) $elementAttributes['disabled'] = true;
    }

    // === CONTENT ===
    $displayText = $loading ? $loadingText : ($text ?? $slot);
@endphp

<{{ $element }} {{ $attributes->merge($elementAttributes)->twMerge($finalClasses) }}>
    {{-- Avatar --}}
    @if($withAvatar && !$loading)
        @if($avatarSrc || $avatarName)
            <x-avatar
                :src="$avatarSrc"
                :name="$avatarName"
                :size="$avatarSize"
                :theme="$theme"
            />
        @else
            {{ $avatar ?? '' }}
        @endif
    @endif

    {{-- Loading Spinner --}}
    @if($loading)
        <x-spinner
            :size="match($size) {
                'xs' => 'xs',
                'sm' => 'sm',
                'md' => 'sm',
                'lg' => 'md',
                'xl' => 'lg',
                default => 'sm'
            }"
            :color="match($variant) {
                'solid' => 'white',
                'outlined', 'ghost', 'soft', 'link' => $color,
                'white' => $color,
                default => 'white'
            }"
            :theme="$theme"
        />
    @endif

    {{-- Icon Left --}}
    @if($icon && $iconPosition === 'left' && !$loading)
        <x-icon-wrap
            :size="match($iconSize) {
                'xs' => '8',
                'sm' => '9',
                'md' => '10',
                'lg' => '11',
                'xl' => '12',
                default => '5'
            }"
            color="currentColor"
            variant="ghost"
        >
            @svg($icon, 'w-4 h-4')
        </x-icon-wrap>
    @endif

    {{-- Content --}}
    @if($text || $loading)
        {{ $displayText }}
    @else
        {{ $slot }}
    @endif

    {{-- Icon Right --}}
    @if($icon && $iconPosition === 'right' && !$loading)
    <x-icon-wrap
            :size="match($iconSize) {
                'xs' => '8',
                'sm' => '9',
                'md' => '10',
                'lg' => '11',
                'xl' => '12',
                default => '5'
            }"
            color="currentColor"
            variant="ghost"
        >
            @svg($icon, 'w-4 h-4')
        </x-icon-wrap>
    @endif
</{{ $element }}>
