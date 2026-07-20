{{--
    Scroll To Component - Pure Preline UI

    Simple wrapper around Preline UI native scroll functionality
    No custom JavaScript - uses Preline components directly

    Usage:
    <x-scroll-to target="#section-1">Click me</x-scroll-to>
    <x-scroll-to target="#top">Back to top</x-scroll-to>
    <x-scroll-to scrollspy="#sections" target="#item-1" active-class="text-blue-600">Nav Item</x-scroll-to>
--}}
@props([
    'target' => '#top',                 // Target selector
    'scrollspy' => null,                // Scrollspy container ID (for navigation)
    'activeClass' => '',                // Active state classes for scrollspy
    'class' => '',                      // Additional classes
])

@if($scrollspy)
{{-- Preline Scrollspy Navigation Link --}}
<a href="{{ $target }}"
   data-hs-scrollspy="{{ $scrollspy }}"
   class="hs-scrollspy-active:{{ $activeClass ?: 'text-blue-600 font-medium' }} {{ $class }}"
   {{ $attributes }}>
    {{ $slot }}
</a>
@else
{{-- Simple Scroll Link --}}
<a href="{{ $target }}"
   class="{{ $class }}"
   {{ $attributes }}>
    {{ $slot }}
</a>
@endif