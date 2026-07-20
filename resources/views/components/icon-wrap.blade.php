{{--
    Icon Wrap Component - Preline UI Compatible

    Static icon wrapper following Preline exact pattern
    Based on: https://preline.co/docs/static-icons.html

    Safelist Tailwind pour classes dynamiques :
    Sizes: size-8 size-9 size-10 size-11 size-12 size-14 size-16
    Colors: text-blue-600 text-blue-800 bg-blue-600 bg-blue-100 bg-blue-200 border-blue-600 border-blue-100
    Shapes: rounded-full
--}}
@props([
    // Size
    'size' => '11',             // 8, 9, 10, 11, 12, 14, 16 (using size-X class)

    // Color & Theme
    'color' => 'blue',          // blue, red, green, yellow, purple, gray, cinema-gold, cinema-red, cinema-black
    'variant' => 'ghost',       // ghost, solid, soft, soft-outlined, outlined, white

    // Shape
    'shape' => 'rounded-full',  // rounded-full, rounded-lg, rounded-md, rounded-sm, rounded-none

    // Custom Classes
    'class' => '',              // additional classes
])

@php
    $baseClasses = ['inline-flex', 'justify-center', 'items-center'];
    // === SIZE ===
    $baseClasses[] = "size-{$size}";

    // === SHAPE ===
    $baseClasses[] = $shape;

    // === COLOR VARIANTS ===
    $colorClasses = match($variant) {
        // Ghost (just text color)
        'ghost' => match($color) {
            'blue' => 'text-blue-600 dark:text-blue-500',
            'red' => 'text-red-500',
            'green' => 'text-green-500',
            'yellow' => 'text-yellow-500',
            'purple' => 'text-purple-500',
            'gray' => 'text-gray-500 dark:text-neutral-500',
            'gray-dark' => 'text-gray-700 dark:text-neutral-400',
            'teal' => 'text-teal-500',
            'white' => 'text-white',
            'cinema-gold' => 'text-cinema-gold',
            'cinema-red' => 'text-cinema-red',
            'cinema-black' => 'text-cinema-black',
            default => "text-{$color}-500"
        },

        // Solid (background + white text)
        'solid' => match($color) {
            'blue' => 'bg-blue-600 text-white dark:bg-blue-500',
            'red' => 'bg-red-500 text-white',
            'green' => 'bg-green-500 text-white',
            'yellow' => 'bg-yellow-500 text-white',
            'purple' => 'bg-purple-500 text-white',
            'gray' => 'bg-gray-500 text-white dark:bg-neutral-700',
            'gray-dark' => 'bg-gray-800 text-white dark:bg-white dark:text-neutral-800',
            'teal' => 'bg-teal-500 text-white',
            'white' => 'bg-white text-gray-600',
            'cinema-gold' => 'bg-cinema-gold text-cinema-black',
            'cinema-red' => 'bg-cinema-red text-white',
            'cinema-black' => 'bg-cinema-black text-cinema-gold',
            default => "bg-{$color}-500 text-white"
        },

        // Soft (light background + dark text)
        'soft' => match($color) {
            'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-400',
            'red' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-400',
            'green' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-400',
            'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-400',
            'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-400',
            'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-400',
            'cinema-gold' => 'bg-cinema-gold/20 text-cinema-gold',
            'cinema-red' => 'bg-cinema-red/20 text-cinema-red',
            'cinema-black' => 'bg-cinema-black/20 text-cinema-black',
            default => "bg-{$color}-100 text-{$color}-800"
        },

        // Soft-outlined (soft + border)
        'soft-outlined' => match($color) {
            'blue' => 'border-4 border-blue-100 bg-blue-200 text-blue-800 dark:border-blue-900 dark:bg-blue-800 dark:text-blue-400',
            'red' => 'border-4 border-red-100 bg-red-200 text-red-800 dark:border-red-900 dark:bg-red-800 dark:text-red-400',
            'green' => 'border-4 border-green-100 bg-green-200 text-green-800 dark:border-green-900 dark:bg-green-800 dark:text-green-400',
            'yellow' => 'border-4 border-yellow-100 bg-yellow-200 text-yellow-800 dark:border-yellow-900 dark:bg-yellow-800 dark:text-yellow-400',
            'purple' => 'border-4 border-purple-100 bg-purple-200 text-purple-800 dark:border-purple-900 dark:bg-purple-800 dark:text-purple-400',
            'gray' => 'border-4 border-gray-100 bg-gray-200 text-gray-800 dark:border-gray-900 dark:bg-gray-800 dark:text-gray-400',
            'cinema-gold' => 'border-4 border-cinema-gold/30 bg-cinema-gold/10 text-cinema-gold',
            'cinema-red' => 'border-4 border-cinema-red/30 bg-cinema-red/10 text-cinema-red',
            'cinema-black' => 'border-4 border-cinema-black/30 bg-cinema-black/10 text-cinema-black',
            default => "border-4 border-{$color}-100 bg-{$color}-200 text-{$color}-800"
        },

        // Outlined (border only)
        'outlined' => match($color) {
            'blue' => 'border border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-500',
            'red' => 'border border-red-500 text-red-500',
            'green' => 'border border-green-500 text-green-500',
            'yellow' => 'border border-yellow-500 text-yellow-500',
            'purple' => 'border border-purple-500 text-purple-500',
            'gray' => 'border border-gray-500 text-gray-500 dark:text-neutral-500',
            'gray-dark' => 'border border-gray-700 text-gray-700 dark:border-neutral-200 dark:text-neutral-200',
            'teal' => 'border border-teal-500 text-teal-500',
            'white' => 'border border-white text-white',
            'cinema-gold' => 'border border-cinema-gold text-cinema-gold',
            'cinema-red' => 'border border-cinema-red text-cinema-red',
            'cinema-black' => 'border border-cinema-black text-cinema-black',
            default => "border border-{$color}-500 text-{$color}-500"
        },

        // White (white background with shadow)
        'white' => 'border border-gray-200 bg-white text-gray-700 shadow-2xs dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400',

        default => "text-{$color}-600"
    };

    $baseClasses[] = $colorClasses;

    $finalClasses = collect($baseClasses)
        ->push($class)
        ->filter()
        ->implode(' ');
@endphp

<span {{ $attributes->twMerge($finalClasses) }}>
    {{ $slot }}
</span>
