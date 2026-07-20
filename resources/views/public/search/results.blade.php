@if($hasResults)
    <div class="absolute top-full left-0 right-0 bg-gray-900/98 backdrop-blur-sm border border-gray-700 rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto">
        <div class="p-4">
            <div class="text-sm text-gray-400 mb-3">{{ $films->count() }} résultat(s) pour "{{ $query }}"</div>

            <div class="space-y-3">
                @foreach($films as $film)
                    <a href="{{ route('films.show', $film->film_id) }}"
                       class="flex gap-3 p-3 rounded-lg bg-gray-800/50 hover:bg-gray-700/50 transition-colors group">
                        <!-- Affiche miniature -->
                        @if($film->affiche_url)
                            <div class="flex-shrink-0">
                                <img src="{{ $film->affiche_url }}"
                                     alt="{{ $film->titre }}"
                                     class="w-12 h-16 rounded object-cover">
                            </div>
                        @endif

                        <!-- Infos film -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-medium group-hover:text-gold transition-colors truncate">
                                {{ $film->titre }}
                            </h3>

                            <div class="flex items-center gap-3 text-sm text-gray-400 mt-1">
                                @if($film->note_moyenne > 0)
                                    <span class="flex items-center gap-1">
                                        ⭐ {{ number_format($film->note_moyenne, 1) }}
                                    </span>
                                @endif

                                @if($film->duree_minutes)
                                    <span>{{ $film->duree_minutes }}min</span>
                                @endif

                                <span class="text-gray-500">{{ $film->genre }}</span>

                                @if($film->classification)
                                    <x-classification-badge :value="$film->classification" class="px-1.5 py-0.5 text-xs" />
                                @endif
                            </div>

                            @if($film->synopsis)
                                <p class="text-gray-400 text-sm mt-1 line-clamp-2">{{ $film->synopsis }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Lien voir tous -->
            <div class="mt-4 pt-3 border-t border-gray-700">
                <a href="{{ route('films.index', ['search' => $query]) }}"
                   class="block text-center text-gold hover:text-yellow-400 text-sm font-medium transition-colors">
                    Voir tous les résultats →
                </a>
            </div>
        </div>
    </div>
@elseif(strlen($query) >= 2)
    <div class="absolute top-full left-0 right-0 bg-gray-900/98 backdrop-blur-sm border border-gray-700 rounded-lg shadow-xl z-50">
        <div class="p-4 text-center">
            <div class="text-gray-400 text-sm">Aucun film trouvé pour "{{ $query }}"</div>
        </div>
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