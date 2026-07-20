<!-- Modal de recherche -->
<div id="search-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-gray-900 border border-gray-700 shadow-sm rounded-xl pointer-events-auto">
            <!-- Header -->
            <div class="flex justify-between items-center py-4 px-6 border-b border-gray-700">
                <h3 class="text-lg font-bold text-white">
                    <svg class="w-5 h-5 inline mr-2 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Rechercher un film
                </h3>
                <button type="button" class="flex justify-center items-center size-7 text-sm font-semibold rounded-full border border-transparent text-gray-400 hover:text-white hover:bg-gray-800 transition-colors" data-hs-overlay="#search-modal">
                    <span class="sr-only">Fermer</span>
                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m18 6-12 12"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-6">
                <!-- Search Input -->
                <div class="relative">
                    <input type="text"
                           id="modal-search-input"
                           name="q"
                           placeholder="Titre du film, acteur, réalisateur..."
                           hx-get="{{ route('modal.search') }}"
                           hx-target="#modal-search-results"
                           hx-trigger="keyup changed delay:300ms"
                           hx-swap="innerHTML"
                           class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 pl-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-all"
                           autocomplete="off">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- Results Container -->
                <div id="modal-search-results" class="min-h-[200px] max-h-[400px] overflow-y-auto">
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <p class="text-gray-400">Commencez à taper pour rechercher un film...</p>
                        <p class="text-gray-500 text-sm mt-2">Tapez au moins 2 caractères</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('search-modal');
    if (modal) {
        // Focus sur l'input quand le modal s'ouvre
        modal.addEventListener('hs.overlay.open', function() {
            setTimeout(() => {
                const searchInput = document.getElementById('modal-search-input');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.value = '';
                    // Clear previous results
                    document.getElementById('modal-search-results').innerHTML = `
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <p class="text-gray-400">Commencez à taper pour rechercher un film...</p>
                            <p class="text-gray-500 text-sm mt-2">Tapez au moins 2 caractères</p>
                        </div>
                    `;
                }
            }, 100);
        });
    }
});
</script>
@endpush