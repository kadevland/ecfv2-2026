<div class="relative" x-data="{ focused: false }">
    <div class="relative">
        <input type="text"
               name="search"
               placeholder="Rechercher un film..."
               @focus="focused = true"
               @blur="setTimeout(() => focused = false, 200)"
               hx-get="{{ route('search') }}"
               hx-target="#search-results"
               hx-trigger="keyup changed delay:300ms, search"
               hx-vals='js:{"q": event.target.value}'
               hx-swap="innerHTML"
               class="w-full pl-10 pr-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-colors">

        <!-- Icône de recherche -->
        <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Zone de résultats -->
    <div id="search-results"></div>
</div>

@push('styles')
<style>
.text-gold {
    color: #d4af37;
}

.focus\:ring-gold:focus {
    --tw-ring-color: #d4af37;
}

.focus\:border-gold:focus {
    border-color: #d4af37;
}
</style>
@endpush