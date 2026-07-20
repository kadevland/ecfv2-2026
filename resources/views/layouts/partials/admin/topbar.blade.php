<!-- Topbar -->
<div class="bg-white shadow-sm border-b border-gray-200">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side -->
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button type="button"
                    class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-admin-accent"
                    aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-sidebar-collapsible-group"
                    aria-label="Toggle navigation" data-hs-overlay="#hs-sidebar-collapsible-group">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Search -->
                {{-- <div class="ml-4 flex items-center">
                    <div class="relative">
                        <input type="text" placeholder="Rechercher..."
                            class="w-64 bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 pl-10 text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-admin-accent focus:border-transparent">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div> --}}
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-4">
                {{-- <!-- Quick Actions -->
                <div class="hidden md:flex items-center space-x-2">
                    <x-button variant="ghost" color="gray" theme="admin" size="sm" icon="heroicon-o-plus"
                        iconPosition="left">
                        Nouveau
                    </x-button>
                </div> --}}

                <!-- Notifications -->

                <x-admin.top-notifications />

                <!-- User menu -->

                <x-admin.top-menu-user />

            </div>
        </div>
    </div>
</div>
