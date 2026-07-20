{{--
    Avatar Component - Preline UI Compatible

    Avatar component for user profiles and display
    Based on: https://preline.co/docs/avatar.html

    Safelist Tailwind pour classes dynamiques :
    Sizes: w-6 h-6 w-8 h-8 w-10 h-10 w-12 h-12 w-14 h-14 w-16 h-16 w-20 h-20 w-24 h-24 w-32 h-32
    Text sizes: text-xs text-sm text-base text-lg text-xl text-2xl text-3xl
    Border widths: border border-2 border-4 ring-1 ring-2 ring-4
    Status positions: top-0 right-0 bottom-0 left-0 -top-1 -right-1 -bottom-1 -left-1
    Tooltip positions: bottom-full top-full left-full right-full transform -translate-x-1/2 -translate-y-1/2
    Tooltip themes: bg-white bg-gray-900 bg-cinema-black bg-gray-800 text-gray-900 text-white text-cinema-gold
    Transitions: opacity-0 opacity-100 invisible visible group-hover:opacity-100 group-hover:visible duration-200
--}}
@props([
    // Avatar Source & Content
    'image' => null, // Alias for 'src' and mediaType
    'src' => null, // Image source URL
    'alt' => '', // Alt text for image
    'name' => null, // Name for initials fallback
    'initials' => null, // Custom initials (overrides name)
    'icon' => null, // Icon component/class for fallback

    // Sizing
    'size' => 'md', // xs, sm, md, lg, xl, 2xl, 3xl, custom
    'width' => null, // Custom width (overrides size)
    'height' => null, // Custom height (overrides size)

    // Shape & Border
    'rounded' => 'full', // none, sm, md, lg, xl, 2xl, 3xl, full, square
    'border' => null, // Border color and width
    'ring' => null, // Ring color and width

    // Status Indicator
    'status' => null, // online, offline, away, busy, null
    'statusPosition' => 'bottom-right', // top-left, top-right, bottom-left, bottom-right
    'statusSize' => null, // Size of status indicator
    'customStatus' => null, // Custom status color

    // Background & Styling
    'background' => null, // Background color for initials/icon
    'textColor' => null, // Text color for initials
    'theme' => 'cinema', // cinema, admin
    'variant' => 'default', // default, outlined, soft, minimal

    // Interactive States
    'clickable' => false, // Make avatar clickable
    'hover' => null, // Hover effects
    'active' => null, // Active state styling

    // Group Avatar
    'group' => false, // Part of avatar group
    'groupIndex' => null, // Index in group (for stacking)
    'groupOffset' => null, // Negative margin for overlap

    // Tooltip Support
    'tooltip' => null, // Tooltip text content
    'tooltipPosition' => 'top', // top, bottom, left, right
    'tooltipTheme' => null, // dark, light, or inherit from theme

    // Media Support
    'mediaType' => 'image', // image, video, iframe
    'videoSrc' => null, // Video source for video type
    'iframeSrc' => null, // Iframe source for iframe type
    'autoplay' => false, // Autoplay for video
    'loop' => false, // Loop for video
    'muted' => true, // Muted for video

    // Accessibility
    'role' => null, // ARIA role
    'tabindex' => null, // Tab index for keyboard navigation

    // Custom Classes
    'class' => '', // Additional classes
])

@php
    $baseClasses = ['relative', 'inline-flex', 'items-center', 'justify-center', 'flex-shrink-0'];
    $imgClasses = ['w-full', 'h-full', 'object-cover'];
    $statusClasses = ['absolute', 'rounded-full', 'border-2', 'border-white'];

    // === THEME PRESETS ===
    $themePresets = [
        'cinema' => [
            'default' => [
                'background' => 'bg-cinema-gold',
                'textColor' => 'text-cinema-black',
                'border' => 'border-cinema-gold',
            ],
            'outlined' => [
                'background' => 'bg-transparent',
                'textColor' => 'text-cinema-gold',
                'border' => 'border-2 border-cinema-gold',
            ],
            'soft' => [
                'background' => 'bg-cinema-gold/20',
                'textColor' => 'text-cinema-gold',
                'border' => 'border-cinema-gold/30',
            ],
        ],
        'admin' => [
            'default' => [
                'background' => 'bg-gray-500',
                'textColor' => 'text-white',
                'border' => 'border-gray-300',
            ],
            'outlined' => [
                'background' => 'bg-transparent',
                'textColor' => 'text-gray-600',
                'border' => 'border-2 border-gray-300',
            ],
            'soft' => [
                'background' => 'bg-gray-100',
                'textColor' => 'text-gray-600',
                'border' => 'border-gray-200',
            ],
        ],
    ];

    // Apply theme preset
    if (isset($themePresets[$theme][$variant])) {
        $preset = $themePresets[$theme][$variant];
        $background = $background ?? $preset['background'];
        $textColor = $textColor ?? $preset['textColor'];
        if (!$border && isset($preset['border'])) {
            $border = $preset['border'];
        }
    }

    // === SIZING ===
    $sizeMap = [
        'xs' => ['size' => 'w-6 h-6', 'text' => 'text-xs'],
        'sm' => ['size' => 'w-8 h-8', 'text' => 'text-sm'],
        'md' => ['size' => 'w-10 h-10', 'text' => 'text-sm'],
        'lg' => ['size' => 'w-12 h-12', 'text' => 'text-base'],
        'xl' => ['size' => 'w-14 h-14', 'text' => 'text-lg'],
        '2xl' => ['size' => 'w-16 h-16', 'text' => 'text-xl'],
        '3xl' => ['size' => 'w-20 h-20', 'text' => 'text-2xl'],
    ];

    if (isset($sizeMap[$size])) {
        $baseClasses[] = $sizeMap[$size]['size'];
        $textSize = $sizeMap[$size]['text'];
    } else {
        // Custom size
        if ($width) {
            $baseClasses[] = str_starts_with($width, 'w-') ? $width : "w-{$width}";
        }
        if ($height) {
            $baseClasses[] = str_starts_with($height, 'h-') ? $height : "h-{$height}";
        }
        $textSize = 'text-sm';
    }

    // === SHAPE ===
    if ($rounded !== null) {
        $roundedClass = match ($rounded) {
            'none' => 'rounded-none',
            'square' => 'rounded-none',
            'sm' => 'rounded-sm',
            'md' => 'rounded-md',
            'lg' => 'rounded-lg',
            'xl' => 'rounded-xl',
            '2xl' => 'rounded-2xl',
            '3xl' => 'rounded-3xl',
            'full' => 'rounded-full',
            default => $rounded,
        };
        $baseClasses[] = $roundedClass;
        $imgClasses[] = $roundedClass;
    }

    // === BACKGROUND & TEXT ===
    if ($background) {
        $baseClasses[] = $background;
    }
    if ($textColor) {
        $baseClasses[] = $textColor;
    }
    if ($textSize) {
        $baseClasses[] = $textSize;
    }

    // === BORDER & RING ===
    if ($border) {
        $baseClasses[] = $border;
    }
    if ($ring) {
        $baseClasses[] = $ring;
    }

    // === INTERACTIVE ===
    if ($clickable) {
        $baseClasses[] = 'cursor-pointer';
        $baseClasses[] = 'transition-all duration-200';
        if (!$hover) {
            $baseClasses[] = 'hover:scale-105 hover:shadow-lg';
        }
    }
    if ($hover) {
        $baseClasses[] = $hover;
    }
    if ($active) {
        $baseClasses[] = $active;
    }

    // === GROUP HANDLING ===
    if ($group && $groupOffset) {
        $baseClasses[] = $groupOffset;
    }

    // === TOOLTIP HANDLING (Preline UI) ===
    $tooltipConfig = null;
    if ($tooltip) {
        // Tooltip position for Preline JS
        $position = match ($tooltipPosition) {
            'top' => 'top',
            'bottom' => 'bottom',
            'left' => 'left',
            'right' => 'right',
            default => 'top', // default top
        };

        // Tooltip theme colors
        $tooltipThemeClass = match ($tooltipTheme ?? $theme) {
            'light' => 'bg-white text-gray-900 border border-gray-200 shadow-lg',
            'dark' => 'bg-gray-900 text-white border border-gray-700',
            'cinema' => 'bg-cinema-black text-cinema-gold border border-cinema-gold/30',
            'admin' => 'bg-gray-800 text-white border border-gray-600',
            default => 'bg-gray-900 text-white', // default dark
        };

        $tooltipConfig = [
            'text' => $tooltip,
            'position' => $position,
            'themeClasses' => $tooltipThemeClass,
        ];
    }

    // === STATUS INDICATOR ===
    $statusConfig = null;
    if ($status || $customStatus) {
        $statusColor = match ($status) {
            'online' => 'bg-green-500',
            'offline' => 'bg-gray-400',
            'away' => 'bg-yellow-500',
            'busy' => 'bg-red-500',
            default => $customStatus ?? 'bg-gray-400',
        };

        $statusPosition = match ($statusPosition) {
            'top-left' => 'top-0 left-0',
            'top-right' => 'top-0 right-0',
            'bottom-left' => 'bottom-0 left-0',
            'bottom-right' => 'bottom-0 right-0',
            default => $statusPosition,
        };

        $statusSizeClass = match ($statusSize ?? $size) {
            'xs', 'sm' => 'w-2 h-2',
            'md', 'lg' => 'w-3 h-3',
            'xl', '2xl', '3xl' => 'w-4 h-4',
            default => 'w-3 h-3',
        };

        $statusConfig = [
            'classes' => collect($statusClasses)
                ->push($statusColor)
                ->push($statusPosition)
                ->push($statusSizeClass)
                ->implode(' '),
        ];
    }

    // === GENERATE INITIALS ===
    $displayInitials = null;
    if (!$src && ($name || $initials)) {
        $text = $initials ?? $name;
        if ($text) {
            $words = explode(' ', trim($text));
            if (count($words) >= 2) {
                $displayInitials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
            } else {
                $displayInitials = strtoupper(substr($words[0], 0, 2));
            }
        }
    }

    // === HTML ATTRIBUTES ===
    $htmlAttributes = [];
    if ($role) {
        $htmlAttributes['role'] = $role;
    }
    if ($tabindex !== null) {
        $htmlAttributes['tabindex'] = $tabindex;
    }

    $finalClasses = collect($baseClasses)->push($class)->filter()->implode(' ');

    if ($image && !$src) {
        $src = $image;
        $mediaType = 'image';
    }

@endphp

@if ($tooltipConfig)
    <div class="hs-tooltip [--placement:{{ $tooltipConfig['position'] }}] inline-block">
        <a class="hs-tooltip-toggle relative inline-block {{ $finalClasses }}" href="#"
            @if ($clickable) role="button" @endif>
        @else
            <div {{ $attributes->merge($htmlAttributes)->twMerge($finalClasses) }}
                @if ($clickable) role="button" @endif>
@endif
@if ($src || $mediaType === 'image')
    {{-- Image Avatar --}}
    <img src="{{ $src }}" alt="{{ $alt }}" class="{{ implode(' ', $imgClasses) }}">
@elseif($mediaType === 'video' && $videoSrc)
    {{-- Video Avatar --}}
    <video class="{{ implode(' ', $imgClasses) }}" @if ($autoplay) autoplay @endif
        @if ($loop) loop @endif @if ($muted) muted @endif playsinline>
        <source src="{{ $videoSrc }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
@elseif($mediaType === 'iframe' && $iframeSrc)
    {{-- Iframe Avatar --}}
    <iframe src="{{ $iframeSrc }}" class="{{ implode(' ', $imgClasses) }} border-0" loading="lazy"></iframe>
@elseif($displayInitials)
    {{-- Initials Avatar --}}
    <span class="font-semibold">{{ $displayInitials }}</span>
@elseif($icon)
    {{-- Icon Avatar with Blade UI Kit --}}
    @svg($icon, 'w-full h-full')
@else
    {{-- Default User Icon with Blade UI Kit --}}
    @svg('heroicon-o-user', 'w-full h-full')
@endif

{{-- Status Indicator --}}
@if ($statusConfig)
    <span class="{{ $statusConfig['classes'] }}"></span>
@endif

{{-- Preline Tooltip Content --}}
@if ($tooltipConfig)
    <div class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 text-xs font-medium rounded-lg shadow-sm {{ $tooltipConfig['themeClasses'] }}"
        role="tooltip">
        {{ $tooltipConfig['text'] }}
    </div>
@endif

{{-- Slot for additional content --}}
@if ($slot->isNotEmpty())
    {{ $slot }}
@endif
@if ($tooltipConfig)
    </a>
    </div>
@else
    </div>
@endif
