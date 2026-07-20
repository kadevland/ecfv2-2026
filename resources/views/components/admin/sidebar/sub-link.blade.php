{{--
    Sidebar Sub Link Component - Structure Preline UI

    Sous-lien pour les sections collapsibles de la sidebar admin
    Usage: <x-sidebar-sub-link href="#" :active="false">Liste des cinémas</x-sidebar-sub-link>
--}}
@props([
    'href' => '#',
    'active' => false,
    'class' => '',
])

@php
    // Classes Preline pour sous-liens
    $baseClasses = 'flex items-center gap-x-3.5 py-2 px-2.5 text-sm rounded-lg focus:outline-hidden transition-colors duration-200';
    $stateClasses = $active
        ? 'bg-admin-accent/10 text-admin-accent'
        : 'text-gray-800 hover:bg-gray-100 focus:bg-gray-100';

    $classes = $baseClasses . ' ' . $stateClasses . ' ' . $class;
@endphp

<li>
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
</li>