{{--
    Sidebar Section Component - Structure Preline UI

    Section collapsible avec titre et sous-liens pour la sidebar admin
    Usage:
    <x-sidebar-section title="Cinémas" icon="cinema" id="cinemas-accordion">
        <x-sidebar-sub-link href="#">Liste des cinémas</x-sidebar-sub-link>
        <x-sidebar-sub-link href="#">Ajouter un cinéma</x-sidebar-sub-link>
    </x-sidebar-section>
--}}
@props([
    'title' => '',
    'icon' => null,
    'id' => null,
    'class' => '',
])

@php
    // Icons SVG paths (format Preline)
    // $icons = [
    //     'cinema' => 'M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z M3 6h18 M16 10a4 4 0 0 1-8 0',
    //     'film' => 'M3 3h18v18H3z M7 3v18 M3 7.5h4 M3 12h18 M3 16.5h4 M17 3v18 M17 7.5h4 M17 16.5h4',
    //     'dashboard' => 'm3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z',
    //     'booking' => 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    //     'users' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 7a4 4 0 1 1 0 8 4 4 0 0 1 0-8z M13 7l2 2 4-4',
    // ];

    // $iconPath = $icons[$icon] ?? $icon;
    $accordionId = $id ?: 'accordion-' . Str::random(8);
    $collapseId = $accordionId . '-collapse';
@endphp

{{-- Structure accordéon Preline officielle --}}
<li class="hs-accordion {{ $class }}" id="{{ $accordionId }}">
    <button type="button"
            class="hs-accordion-toggle w-full text-start flex items-center gap-x-3.5 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100"
            aria-expanded="false"
            aria-controls="{{ $collapseId }}">
        @if($icon)

          @svg($icon, 'size-4')
            {{-- <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                @foreach(explode(' M', $iconPath) as $index => $pathPart)
                    @if($index === 0)
                        <path d="{{ $pathPart }}"/>
                    @else
                        <path d="M{{ $pathPart }}"/>
                    @endif
                @endforeach
            </svg> --}}
        @endif
        <span class="hs-overlay-minified:hidden">{{ $title }}</span>

        <svg class="hs-overlay-minified:hidden hs-accordion-active:block ms-auto hidden size-4 text-gray-600 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m18 15-6-6-6 6"/>
        </svg>
        <svg class="hs-overlay-minified:hidden hs-accordion-active:hidden ms-auto block size-4 text-gray-600 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </button>

    <div id="{{ $collapseId }}"
         class="hs-overlay-minified:hidden hs-accordion-content w-full overflow-hidden transition-[height] duration-300 hidden"
         role="region"
         aria-labelledby="{{ $accordionId }}">
        <ul class="pt-1 ps-7 space-y-1">
            {{ $slot }}
        </ul>
    </div>
</li>
