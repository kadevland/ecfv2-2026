<div class="relative" x-data="{ open: false }">
    <button @click="open = !open"
        class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-admin-accent rounded-md">

        <x-heroicon-o-bell class="w-6 h-6" />

        @if (count($notifications) > 0)
            <x-badge positioned="true" position="top-right" color="danger" theme="admin" variant="solid"
                size="xs">{{ count($notifications) }}</x-badge>
        @endif
    </button>

    <!-- Notifications dropdown -->
    <div x-show="open" @click.away="open = false" x-cloak
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
        <div class="px-4 py-2 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
        </div>
        <div class="max-h-64 overflow-y-auto">
            @forelse($notifications as $notification)
                <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if (isset($notification['avatar']))
                                <x-avatar image="{{ $notification['avatar'] }}" size="xs" theme="admin" />
                            @else
                                <div
                                    class="w-8 h-8 rounded-full bg-{{ $notification['type'] === 'success' ? 'green' : ($notification['type'] === 'warning' ? 'yellow' : ($notification['type'] === 'danger' ? 'red' : 'blue')) }}-100 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-{{ $notification['type'] === 'success' ? 'green' : ($notification['type'] === 'warning' ? 'yellow' : ($notification['type'] === 'danger' ? 'red' : 'blue')) }}-600"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        @if ($notification['type'] === 'success')
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        @elseif($notification['type'] === 'warning')
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        @elseif($notification['type'] === 'danger')
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd"></path>
                                        @else
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd"></path>
                                        @endif
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm text-gray-900 truncate">
                                {{ $notification['message'] }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $notification['time'] }}
                            </p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-4 py-6 text-center text-gray-500">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-3.5-3.5a2.828 2.828 0 010-4L20 6M9 3L3 9l6 6 6-6-6-6z"></path>
                    </svg>
                    <p class="text-sm">Aucune notification</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
