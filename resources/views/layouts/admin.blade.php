<!DOCTYPE html>
<html lang="fr" class="h-full admin">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cinéphoria') }} - Admin @yield('title', 'Dashboard')</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="h-full bg-gray-50 text-gray-900 font-sans antialiased admin-theme">

    <!-- Sidebar -->

    @include('layouts.partials.admin.sidebar')


    <!-- Main Content avec marge pour la sidebar -->
    <div class="lg:ml-64 flex flex-col min-h-screen transition-all duration-300 hs-overlay-minified:ml-13">

        <!-- Topbar -->
        @include('layouts.partials.admin.topbar')

        <!-- Page Content -->
        <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
            <!-- Page Header intégré avec breadcrumbs -->
            @if (View::hasSection('breadcrumbs') || View::hasSection('title') || View::hasSection('actions'))
                <div class="bg-gray-50 border-b border-gray-200">
                    <x-container class="py-6">
                        <!-- Breadcrumbs -->
                        @hasSection('breadcrumbs')
                            <div class="mb-4">
                                @yield('breadcrumbs')
                            </div>
                        @endif

                        <!-- Title + Actions -->
                        @if (View::hasSection('title') || View::hasSection('actions'))
                            <div class="flex items-center justify-between">
                                <div>
                                    @hasSection('title')
                                        <h1 class="text-2xl font-semibold text-gray-900">
                                            @yield('title')
                                        </h1>
                                    @endif
                                    @hasSection('subtitle')
                                        <p class="mt-1 text-sm text-gray-600">
                                            @yield('subtitle')
                                        </p>
                                    @endif
                                </div>
                                @hasSection('actions')
                                    <div class="flex items-center space-x-3">
                                        @yield('actions')
                                    </div>
                                @endif
                            </div>
                        @endif
                    </x-container>
                </div>
            @endif

            <!-- Content -->
            <div class="py-6">
                <x-container>
                    @yield('content')
                </x-container>
            </div>
        </main>
    </div>

    @stack('scripts')

</body>

</html>
