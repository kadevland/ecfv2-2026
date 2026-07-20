@props([
    'films' => collect(),
    'title' => 'Films',
    'titleAccent' => '',
    'showAll' => true,
    'showAllUrl' => '#',
    'columns' => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5'
])

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl lg:text-4xl font-bold text-white">
                {{ $title }} @if($titleAccent)<span class="text-cinema-gold">{{ $titleAccent }}</span>@endif
            </h2>
            @if($showAll)
            <a href="{{ $showAllUrl }}" class="text-cinema-gold hover:text-cinema-gold/80 font-medium">
                Voir tous →
            </a>
            @endif
        </div>

        @if($films->isNotEmpty())
        <div class="grid {{ $columns }} gap-6">
            @foreach($films as $film)
            <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden hover:border-cinema-gold transition-colors group">
                <div class="aspect-[2/3] bg-gray-800 relative overflow-hidden">
                    @if($film->affiche_url)
                        <img src="{{ $film->affiche_url }}" alt="{{ $film->titre }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-blue-900 to-purple-900 flex items-center justify-center">
                            <div class="text-6xl opacity-50">🎬</div>
                        </div>
                    @endif

                    @if(isset($film->date_sortie) && $film->date_sortie && $film->date_sortie->isAfter(now()->subDays(30)))
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-cinema-gold text-black">
                                Nouveauté
                            </span>
                        </div>
                    @endif
                </div>

                <div class="p-4">
                    <h3 class="text-lg font-semibold text-white mb-1 line-clamp-2">{{ $film->titre }}</h3>
                    <p class="text-sm text-gray-400 mb-3">
                        {{ ucfirst($film->genre ?? 'Film') }}
                        @if(isset($film->duree) && $film->duree) • {{ $film->duree }}min @endif
                    </p>

                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-1">
                            <svg class="size-4 text-cinema-gold" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-sm text-white">{{ isset($film->note_moyenne) && $film->note_moyenne ? number_format($film->note_moyenne, 1) : 'N/A' }}</span>
                        </div>
                        <span class="text-xs text-gray-300">{{ __($film->classification ?? '') }}</span>
                    </div>

                    <a href="/films/{{ $film->film_id ?? $film->id }}" class="w-full py-2 px-4 text-sm font-medium rounded-lg border border-cinema-gold text-cinema-gold hover:bg-cinema-gold hover:text-black transition block text-center">
                        En savoir plus
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <div class="text-6xl mb-4 opacity-50">🎬</div>
            <h3 class="text-xl font-semibold text-gray-400 mb-2">Aucun film disponible</h3>
            <p class="text-gray-500">Les films seront bientôt disponibles dans cette section.</p>
        </div>
        @endif
    </div>
</section>
