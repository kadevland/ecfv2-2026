<!DOCTYPE html>
<html lang="fr" class="h-full {{ request()->is('admin*') ? 'admin' : 'cinema' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Cinéphoria') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- PHPFlasher Assets -->
    @flasher_render
</head>
<body class="h-full">
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            @if (request()->is('admin*'))
                {{-- Admin Theme --}}
                <div class="bg-white shadow-lg rounded-lg p-8">
                    <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-6">
                        <svg class="h-12 w-12 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">@yield('code')</h1>
                    <h2 class="text-xl font-medium text-gray-700 mb-4">@yield('title')</h2>
                    <p class="text-gray-600 mb-6">@yield('message')</p>
                </div>
            @else
                {{-- Cinema Theme --}}
                <div class="bg-gray-900 border-2 border-yellow-500 shadow-2xl rounded-lg p-8" style="box-shadow: 0 0 30px rgba(212, 175, 55, 0.3);">
                    <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-yellow-500 bg-opacity-20 mb-6">
                        <svg class="h-12 w-12 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold text-yellow-500 mb-2" style="font-family: var(--font-cinema-title);">@yield('code')</h1>
                    <h2 class="text-xl font-medium text-yellow-300 mb-4">@yield('title')</h2>
                    <p class="text-gray-300 mb-6">@yield('message')</p>
                </div>
            @endif

            {{-- Loading placeholder while SweetAlert loads --}}
            <div id="loading-placeholder" class="mt-4">
                <div class="animate-pulse">
                    <div class="h-4 bg-gray-300 rounded w-3/4 mx-auto"></div>
                </div>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>