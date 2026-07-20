@extends('layouts.admin')

@section('title', 'Modifier ' . $film->titre)

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.films.index') }}" class="hover:text-gray-900">Films</a>
        <span>/</span>
        <a href="{{ route('admin.films.show', $film->uuid) }}" class="hover:text-gray-900">{{ $film->titre }}</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Modifier</span>
    </nav>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.films.update', $film->uuid) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Hidden field for sous_titres (Domain expects it but we don't edit it) --}}
        <input type="hidden" name="sous_titres" value="">

        <x-card theme="admin" variant="shadow" class="bg-white">
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900">Informations générales</h3>
            </x-slot:header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Titre original --}}
                <div>
                    <label for="titre_original" class="block text-sm font-medium text-gray-700 mb-2">
                        Titre original <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="titre_original"
                        name="titre_original"
                        value="{{ old('titre_original', $film->titre) }}"
                        required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('titre') border-red-300 @enderror"
                    >
                    @error('titre_original')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Titre français --}}
                <div>
                    <label for="titre_original" class="block text-sm font-medium text-gray-700 mb-2">
                        Titre français
                    </label>
                    <input
                        type="text"
                        id="titre"
                        name="titre"
                        value="{{ old('titre', $film->titreFr) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('titre_fr') border-red-300 @enderror"
                    >
                    @error('titre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Réalisateurs --}}
                <div>
                    <label for="realisateurs" class="block text-sm font-medium text-gray-700 mb-2">
                        Réalisateurs <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="realisateurs"
                        name="realisateurs"
                        rows="3"
                        required
                        placeholder="Saisissez un réalisateur par ligne"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('realisateurs') border-red-300 @enderror"
                    >{{ old('realisateurs', implode("\n", $film->realisateurs)) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Un réalisateur par ligne</p>
                    @error('realisateurs')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                 {{-- Acteurs principaux --}}
                <div>
                    <label for="acteurs_principaux" class="block text-sm font-medium text-gray-700 mb-2">
                        Acteurs principaux
                    </label>
                    <textarea
                        id="acteurs_principaux"
                        name="acteurs_principaux"
                        rows="3"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('acteurs_principaux') border-red-300 @enderror"
                        placeholder="Liste des acteurs principaux"
                    >{{ old('acteurs_principaux',implode("\n", $film->acteursPrincipaux)) }}</textarea>
                    @error('acteurs_principaux')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Genre --}}
                <div>
                    <label for="genre" class="block text-sm font-medium text-gray-700 mb-2">
                        Genre <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="genre"
                        name="genre[]"
                        required
                        multiple
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('genre') border-red-300 @enderror"
                    >
                        <option value="" disabled>Sélectionnez un genre</option>
                        @foreach(\App\Domain\Enums\GenreFilm::cases() as $genreOption)
                            <option value="{{ $genreOption->value }}"  @selected(in_array($genreOption->value ,old('genre', $film->genres ?? [])))>
                                {{ $genreOption->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('genre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Durée --}}
                <div>
                    <label for="duree_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Durée (minutes) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="duree_minutes"
                        name="duree_minutes"
                        value="{{ old('duree_minutes', $film->dureeMinutes) }}"
                        required
                        min="1"
                        max="1000"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('duree_minutes') border-red-300 @enderror"
                    >
                    @error('duree_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Classification --}}
                <div>
                    <label for="classification" class="block text-sm font-medium text-gray-700 mb-2">
                        Classification <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="classification"
                        name="classification"
                        required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('classification') border-red-300 @enderror"
                    >
                        <option value="">Sélectionnez</option>
                        @foreach(\App\Domain\Enums\ClassificationFilm::cases() as $classification)
                            <option value="{{ $classification->value }}" {{ old('classification', $film->classification) === $classification->value ? 'selected' : '' }}>
                                {{ $classification->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('classification')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date de sortie --}}
                <div>
                    <label for="date_sortie" class="block text-sm font-medium text-gray-700 mb-2">
                        Date de sortie <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        id="date_sortie"
                        name="date_sortie"
                        value="{{ old('date_sortie', $film->dateSortie) }}"
                        required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('date_sortie') border-red-300 @enderror"
                    >
                    @error('date_sortie')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Résumé --}}
            <div class="mt-6">
                <label for="synopsis" class="block text-sm font-medium text-gray-700 mb-2">
                    Résumé
                </label>
                <textarea
                    id="synopsis"
                    name="synopsis"
                    rows="4"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('resume') border-red-300 @enderror"
                >{{ old('synopsis', $film->resume) }}</textarea>
                @error('synopsis')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </x-card>

        {{-- Détails techniques et médias --}}
        <x-card theme="admin" variant="shadow" class="bg-white">
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900">Détails techniques et médias</h3>
            </x-slot:header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Langue originale --}}
                <div>
                    <label for="langue_originale" class="block text-sm font-medium text-gray-700 mb-2">
                        Langue originale
                    </label>
                    <input
                        type="text"
                        id="langue_originale"
                        name="langue_originale"
                        value="{{ old('langue_originale', $film->langueOriginale) }}"
                        placeholder="ex: Français, Anglais..."
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('langue_originale') border-red-300 @enderror"
                    >
                    @error('langue_originale')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                {{-- URL de l'affiche --}}
                <div>
                    <label for="affiche_url" class="block text-sm font-medium text-gray-700 mb-2">
                        URL de l'affiche
                    </label>
                    <input
                        type="url"
                        id="affiche_url"
                        name="affiche_url"
                        value="{{ old('affiche_url', $film->afficheUrl) }}"
                        placeholder="https://exemple.com/affiche.jpg"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('affiche_url') border-red-300 @enderror"
                    >
                    @error('affiche_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- URL de la bande-annonce --}}
                <div>
                    <label for="bande_annonce_url" class="block text-sm font-medium text-gray-700 mb-2">
                        URL de la bande-annonce
                    </label>
                    <input
                        type="url"
                        id="bande_annonce_url"
                        name="bande_annonce_url"
                        value="{{ old('bande_annonce_url', $film->bandeAnnonceUrl) }}"
                        placeholder="https://youtube.com/watch?v=..."
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('bande_annonce_url') border-red-300 @enderror"
                    >
                    @error('bande_annonce_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-card>

        {{-- Statut --}}
        <x-card theme="admin" variant="shadow" class="bg-white">
            <div class="flex items-center">
                <input
                    type="checkbox"
                    id="est_actif"
                    name="est_actif"
                    value="1"
                    {{ old('est_actif', $film->estActif) ? 'checked' : '' }}
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                >
                <label for="est_actif" class="ml-2 block text-sm text-gray-900">
                    Film actif (disponible à la programmation)
                </label>
            </div>
        </x-card>

        {{-- Actions --}}
        <div class="flex items-center justify-end space-x-4">
            <x-button
                href="{{ route('admin.films.show', $film->uuid) }}"
                color="secondary"
                theme="admin"
                type="button"
            >
                Annuler
            </x-button>
            <x-button
                type="submit"
                color="primary"
                theme="admin"
                icon="heroicon-o-check"
            >
                Mettre à jour
            </x-button>
        </div>
    </form>
@endsection
