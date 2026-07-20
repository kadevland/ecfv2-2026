{{--
    Container Component - Preline UI inspired

    Simple container component with responsive max-widths
    Based on: https://preline.co/docs/container.html
--}}
@props([
    'size' => 'default',     // default, sm, md, lg, xl, 2xl, fluid
    'centered' => true,       // Centrer automatiquement avec mx-auto
    'padded' => true,        // Ajouter du padding horizontal responsive
    'class' => '',           // Classes additionnelles
])

@php
    // Déterminer la classe de taille du container
    $containerClass = match($size) {
        'sm' => 'max-w-screen-sm',
        'md' => 'max-w-screen-md',
        'lg' => 'max-w-screen-lg',
        'xl' => 'max-w-screen-xl',
        '2xl' => 'max-w-screen-2xl',
        'fluid' => 'w-full',
        default => 'container', // Utilise les breakpoints Tailwind par défaut
    };

    // Construire les classes de base
    $baseClasses = collect([
        $containerClass,
        $centered ? 'mx-auto' : '',
        $padded ? 'px-4 sm:px-6 lg:px-8' : '',
        $class,
    ])->filter()->implode(' ');
@endphp

<div {{ $attributes->twMerge($baseClasses) }}>
    {{ $slot }}
</div>