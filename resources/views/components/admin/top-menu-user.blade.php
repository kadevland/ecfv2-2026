        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-admin-accent">

                @if (isset($avatar))
                    <x-avatar image="{{ $avatar }}" size="xs" theme="admin" />
                @endif

                <div class="hidden md:block text-left">
                    <div class="text-sm font-medium text-gray-900">{{ $fullname }}</div>
                    <div class="text-xs text-gray-500">{{ $role }}</div>
                </div>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- User dropdown -->
            <div x-show="open" @click.away="open = false" x-cloak
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                <div class="px-4 py-2 border-b border-gray-200">
                    <p class="text-sm font-medium text-gray-900">{{ $fullname }}</p>
                </div>
                @foreach ($menus as $menu)
                    <a href="{{ $menu['link'] }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                        @if (isset($menu['icon']))
                            <x-icon :name="$menu['icon']" class="w-4 h-4 mr-3 text-gray-400" />
                        @endif
                        {{ $menu['label'] }}
                    </a>
                @endforeach
                <x-divider theme="admin" color="gray-200" />
                <form method="POST" action="/logout" class="w-full">
                    @csrf
                    <button type="submit" class="w-full block px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center text-left">
                        <x-uiw-logout  class="w-4 h-4 mr-3 text-red-400"/>
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>
