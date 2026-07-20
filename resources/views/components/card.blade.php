{{--
    Card Component - Preline UI Compatible

    Card component following Preline pattern
    Based on: https://preline.co/docs/card.html

    Safelist Tailwind pour classes dynamiques :
    Sizes: p-4 p-6 p-8 text-sm text-base text-lg
    Shadows: shadow-sm shadow-md shadow-lg shadow-xl
    Borders: border border-gray-200 rounded-lg rounded-xl
    Images: object-cover aspect-video aspect-square
--}}
@props([
    // Content
    'title' => null,            // Card title
    'subtitle' => null,         // Card subtitle
    'description' => null,      // Card description text

    // Layout
    'size' => 'md',            // sm, md, lg
    'variant' => 'default',     // default, bordered, shadow, hover, horizontal
    'theme' => 'cinema',        // cinema, admin

    // Visual
    'shadow' => 'md',          // none, sm, md, lg, xl
    'rounded' => 'lg',         // none, sm, md, lg, xl
    'border' => false,         // Show border

    // Image
    'imageSrc' => null,        // Image source
    'imageAlt' => null,        // Image alt text
    'imagePosition' => 'top',  // top, bottom, left, right
    'imageSize' => 'auto',     // auto, cover, contain
    'imageAspect' => 'video',  // video, square, auto

    // Header & Footer
    'hasHeader' => false,      // Show header section
    'hasFooter' => false,      // Show footer section
    'headerDivider' => true,   // Show divider after header
    'footerDivider' => true,   // Show divider before footer

    // Interactive
    'href' => null,           // Link href
    'target' => null,         // Link target
    'clickable' => false,     // Make card clickable (without href)

    // Content sections
    'padding' => 'normal',    // none, sm, normal, lg
    'contentPadding' => null, // Override content padding specifically

    // Custom Classes
    'class' => 'bg-white',            // Additional classes
])

@php
    $baseClasses = ['block', 'w-full'];

    // === SIZE MAPPING ===
    $sizeMap = [
        'sm' => ['padding' => 'p-4', 'titleSize' => 'text-base', 'textSize' => 'text-sm'],
        'md' => ['padding' => 'p-6', 'titleSize' => 'text-lg', 'textSize' => 'text-base'],
        'lg' => ['padding' => 'p-8', 'titleSize' => 'text-xl', 'textSize' => 'text-lg'],
    ];

    $sizeConfig = $sizeMap[$size] ?? $sizeMap['md'];

    // === PADDING MAPPING ===
    $paddingMap = [
        'none' => '',
        'sm' => 'p-4',
        'normal' => $sizeConfig['padding'],
        'lg' => 'p-8',
    ];

    $cardPadding = $paddingMap[$padding] ?? $paddingMap['normal'];
    $actualContentPadding = $contentPadding ?? $cardPadding;

    // === VARIANT & THEME STYLING ===
    $variantClasses = match($variant) {
        'default' => [
            'cinema' => 'bg-cinema-black/50 backdrop-blur-sm',
            'admin' => 'bg-white',
        ],
        'bordered' => [
            'cinema' => 'bg-cinema-black/50 backdrop-blur-sm border border-cinema-gold/20',
            'admin' => 'bg-white border border-gray-200',
        ],
        'shadow' => [
            'cinema' => 'bg-cinema-black/50 backdrop-blur-sm shadow-lg shadow-black/25',
            'admin' => 'bg-white shadow-md',
        ],
        'hover' => [
            'cinema' => 'bg-cinema-black/50 backdrop-blur-sm hover:bg-cinema-black/70 transition-all duration-200',
            'admin' => 'bg-white hover:shadow-md transition-all duration-200',
        ],
        'horizontal' => [
            'cinema' => 'bg-cinema-black/50 backdrop-blur-sm',
            'admin' => 'bg-white',
        ],
        default => [
            'cinema' => 'bg-cinema-black/50 backdrop-blur-sm',
            'admin' => 'bg-white',
        ]
    };

    $themeClasses = $variantClasses[$variant][$theme] ?? $variantClasses[$variant]['cinema'] ?? '';
    $baseClasses[] = $themeClasses;

    // === SHADOW ===
    if ($shadow !== 'none') {
        $shadowClasses = match($shadow) {
            'sm' => 'shadow-sm',
            'md' => 'shadow-md',
            'lg' => 'shadow-lg',
            'xl' => 'shadow-xl',
            default => 'shadow-md'
        };
        $baseClasses[] = $shadowClasses;
    }

    // === BORDER ===
    if ($border && $variant !== 'bordered') {
        $borderClasses = match($theme) {
            'cinema' => 'border border-cinema-gold/20',
            'admin' => 'border border-gray-200',
            default => 'border border-gray-200'
        };
        $baseClasses[] = $borderClasses;
    }

    // === ROUNDED ===
    $roundedClasses = match($rounded) {
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        default => 'rounded-lg'
    };
    $baseClasses[] = $roundedClasses;

    // === LAYOUT ===
    if ($variant === 'horizontal') {
        $baseClasses[] = 'flex';
        if ($imagePosition === 'left' || $imagePosition === 'right') {
            $baseClasses[] = 'flex-row';
        }
    }

    // === INTERACTIVE ===
    if ($href || $clickable) {
        $baseClasses[] = 'cursor-pointer transition-all duration-200';
    }

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');

    // === IMAGE CONFIG ===
    $imageConfig = null;
    if ($imageSrc) {
        $aspectClasses = match($imageAspect) {
            'video' => 'aspect-video',
            'square' => 'aspect-square',
            'auto' => '',
            default => 'aspect-video'
        };

        $sizeClasses = match($imageSize) {
            'cover' => 'object-cover',
            'contain' => 'object-contain',
            'auto' => '',
            default => 'object-cover'
        };

        $imageConfig = [
            'src' => $imageSrc,
            'alt' => $imageAlt ?? '',
            'classes' => "w-full {$aspectClasses} {$sizeClasses} {$roundedClasses}"
        ];
    }

    // === WRAPPER ELEMENT ===
    $element = $href ? 'a' : 'div';
    $elementAttributes = [];
    if ($href) {
        $elementAttributes['href'] = $href;
        if ($target) $elementAttributes['target'] = $target;
    }
@endphp

<{{ $element }} {{ $attributes->merge($elementAttributes)->twMerge($finalClasses) }}>
    {{-- Image Top --}}
    @if($imageConfig && $imagePosition === 'top')
        <div class="overflow-hidden {{ $variant === 'horizontal' ? '' : $roundedClasses }}">
            <x-image
                :src="$imageConfig['src']"
                :alt="$imageConfig['alt']"
                :class="$imageConfig['classes']"
            />
        </div>
    @endif

    {{-- Horizontal Layout with Left Image --}}
    @if($variant === 'horizontal' && $imageConfig && $imagePosition === 'left')
        <div class="flex-shrink-0 w-1/3 overflow-hidden {{ $roundedClasses }}">
            <x-image
                :src="$imageConfig['src']"
                :alt="$imageConfig['alt']"
                :class="$imageConfig['classes']"
            />
        </div>
    @endif

    {{-- Main Content Container --}}
    <div class="{{ $variant === 'horizontal' ? 'flex-1' : '' }}">
        {{-- Header Section --}}
        @if($hasHeader)
            <div class="{{ $actualContentPadding }} {{ $headerDivider ? 'border-b' : '' }} {{ $theme === 'cinema' ? 'border-cinema-gold/20' : 'border-gray-200' }}">
                {{ $header ?? '' }}
            </div>
        @endif

        {{-- Content Section --}}
        <div class="{{ $actualContentPadding }} {{ $hasHeader && $headerDivider ? 'border-t-0' : '' }}">
            {{-- Title & Subtitle --}}
            @if($title || $subtitle)
                <div class="mb-4">
                    @if($title)
                        <x-text
                            :size="$sizeConfig['titleSize']"
                            weight="bold"
                            :color="$theme === 'cinema' ? 'white' : 'gray-900'"
                            :theme="$theme"
                            class="mb-2"
                        >
                            {{ $title }}
                        </x-text>
                    @endif

                    @if($subtitle)
                        <x-text
                            :size="$sizeConfig['textSize']"
                            weight="medium"
                            :color="$theme === 'cinema' ? 'gray-300' : 'gray-600'"
                            :theme="$theme"
                        >
                            {{ $subtitle }}
                        </x-text>
                    @endif
                </div>
            @endif

            {{-- Description --}}
            @if($description)
                <x-text
                    :size="$sizeConfig['textSize']"
                    :color="$theme === 'cinema' ? 'gray-400' : 'gray-700'"
                    :theme="$theme"
                    class="mb-4"
                >
                    {{ $description }}
                </x-text>
            @endif

            {{-- Main Content Slot --}}
            {{ $slot }}
        </div>

        {{-- Footer Section --}}
        @if($hasFooter)
            <div class="{{ $actualContentPadding }} {{ $footerDivider ? 'border-t' : '' }} {{ $theme === 'cinema' ? 'border-cinema-gold/20' : 'border-gray-200' }}">
                {{ $footer ?? '' }}
            </div>
        @endif
    </div>

    {{-- Horizontal Layout with Right Image --}}
    @if($variant === 'horizontal' && $imageConfig && $imagePosition === 'right')
        <div class="flex-shrink-0 w-1/3 overflow-hidden {{ $roundedClasses }}">
            <x-image
                :src="$imageConfig['src']"
                :alt="$imageConfig['alt']"
                :class="$imageConfig['classes']"
            />
        </div>
    @endif

    {{-- Image Bottom --}}
    @if($imageConfig && $imagePosition === 'bottom')
        <div class="overflow-hidden {{ $variant === 'horizontal' ? '' : $roundedClasses }}">
            <x-image
                :src="$imageConfig['src']"
                :alt="$imageConfig['alt']"
                :class="$imageConfig['classes']"
            />
        </div>
    @endif
</{{ $element }}>
