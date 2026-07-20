@if($hasResults)
    <div class="space-y-3">
        <p class="text-gray-400 text-sm mb-3">{{ $films->count() }} résultat(s) pour "{{ $query }}"</p>

        @foreach($films as $film)
            <a href="{{ route('films.show', $film->film_id) }}"
               class="flex items-center gap-4 p-3 bg-gray-800/50 rounded-lg hover:bg-gray-700/50 transition-colors group"
               onclick="document.querySelector('[data-hs-overlay=&quot;#search-modal&quot;]').click()">

                <!-- Affiche miniature -->
                <div class="flex-shrink-0">
                    @if($film->affiche_url)
                        <img src="{{ $film->affiche_url }}"
                             alt="{{ $film->titre }}"
                             class="w-16 h-20 rounded object-cover">
                    @else
                        <div class="w-16 h-20 bg-gray-700 rounded flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16l13-8-13-8z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Infos film -->
                <div class="flex-1 min-w-0">
                    <h4 class="text-white font-semibold group-hover:text-gold transition-colors text-base mb-1">
                        {{ $film->titre }}
                    </h4>

                    <div class="text-gray-400 text-sm">
                        @if($film->duree_minutes)
                            <span class="font-medium">{{ $film->duree_minutes }} min</span>
                        @endif

                        @if($film->duree_minutes && $film->genre)
                            <span class="mx-1">•</span>
                        @endif

                        @if($film->genre)
                            <span>{{ $film->genre }}</span>
                        @endif
                    </div>

                    @if($film->note_moyenne > 0)
                        <div class="flex items-center gap-1 mt-1">
                            <span class="text-gold">⭐</span>
                            <span class="text-gold text-sm font-medium">{{ number_format($film->note_moyenne, 1) }}</span>
                        </div>
                    @endif
                </div>
            </a>
        @endforeach

        <!-- Lien voir tous -->
        <div class="mt-4 pt-3 border-t border-gray-700">
            <a href="{{ route('films.index', ['search' => $query]) }}"
               class="block text-center text-gold hover:text-yellow-400 text-sm font-medium transition-colors"
               onclick="document.querySelector('[data-hs-overlay=&quot;#search-modal&quot;]').click()">
                Voir tous les résultats dans le catalogue →
            </a>
        </div>
    </div>
@elseif(strlen($query) >= 2)
    <div class="text-center py-8">
        <svg class="w-12 h-12 mx-auto mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-gray-400 text-sm">Aucun film trouvé pour "{{ $query }}"</p>
        <p class="text-gray-500 text-xs mt-2">Essayez avec d'autres mots-clés</p>
    </div>
@else
    <div class="text-center py-8">
        <svg class="w-12 h-12 mx-auto mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <p class="text-gray-400">Commencez à taper pour rechercher un film...</p>
        <p class="text-gray-500 text-sm mt-2">Tapez au moins 2 caractères</p>
    </div>
@endif

@push('styles')
<style>
.text-gold {
    color: #d4af37;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush