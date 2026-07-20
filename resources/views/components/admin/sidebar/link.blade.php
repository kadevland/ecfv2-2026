{{--
    Sidebar Link Component - Structure Preline UI

    Lien de navigation simple pour sidebar admin
    Usage: <x-sidebar-link href="#" icon="dashboard" :active="true">Dashboard</x-sidebar-link>
--}}
@props([
    'href' => '#',
    'icon' => null,
    'active' => false,
    'badge' => null,
    'class' => '',
])

@php
    // Icons SVG paths (format Preline)
    // $icons = [
    //     'dashboard' => 'm3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z',
    //     'cinema' => 'M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z M3 6h18 M16 10a4 4 0 0 1-8 0',
    //     'film' => 'M3 3h18v18H3z M7 3v18 M3 7.5h4 M3 12h18 M3 16.5h4 M17 3v18 M17 7.5h4 M17 16.5h4',
    //     'ticket' => 'M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2Z M13 5v2 M13 17v2 M13 11v2',
    //     'users' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 7a4 4 0 1 1 0 8 4 4 0 0 1 0-8z M13 7l2 2 4-4',
    //     'staff' => 'M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2 M8.5 7a4 4 0 1 1 0 8 4 4 0 0 1 0-8z M17 11l2 2 4-4M22 4v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4',
    //     'incident' => 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z M12 9v4 M12 17h.01',
    //     'stats' => 'M3 3v18h18 M19 9l-5 5-4-4-3 3',
    //     'settings' => 'M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z M12 12a3 3 0 1 1 0 6 3 3 0 0 1 0-6z',
    // ];

    // $iconPath = $icons[$icon] ?? $icon;

    // Classes Preline
    $baseClasses = 'w-full flex items-center gap-x-3.5 py-2 px-2.5 text-sm rounded-lg focus:outline-hidden transition-colors duration-200';

    $stateClasses = $active
        ? 'bg-admin-accent text-white hover:bg-admin-accent/90 focus:bg-admin-accent/90'
        : 'text-gray-800 hover:bg-gray-100 focus:bg-gray-100';

    $classes = $baseClasses . ' ' . $stateClasses . ' ' . $class;
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)

        @svg($icon, 'size-4 flex-shrink-0')



        {{-- <svg class="size-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            @foreach(explode(' M', $iconPath) as $index => $pathPart)
                @if($index === 0)
                    <path d="{{ $pathPart }}"/>
                @else
                    <path d="M{{ $pathPart }}"/>
                @endif
            @endforeach
        </svg> --}}
    @endif

    <span class="flex-1 hs-overlay-minified:hidden">{{ $slot }}</span>

    @if($badge)
        <span class="ms-auto py-0.5 px-1.5 inline-flex items-center gap-x-1.5 text-xs bg-gray-200 text-gray-800 rounded-full ">
            {{ $badge }}
        </span>
    @endif
</a>
