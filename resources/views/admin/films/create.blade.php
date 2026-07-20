@extends('layouts.admin')

@section('title', 'Nouveau Film')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.films.index') }}" class="hover:text-gray-900">Films</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Nouveau</span>
    </nav>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.films.store') }}" class="space-y-6">
        @csrf

        <x-card theme="admin" variant="shadow" class="bg-white">
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900">Informations générales</h3>
            </x-slot:header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Titre original --}}
                <div>
                    <label for="titre" class="block text-sm font-medium text-gray-700 mb-2">
                        Titre original <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="titre"
                        name="titre"
                        value="{{ old('titre') }}"
                        required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('titre') border-red-300 @enderror"
                        placeholder="Le titre original du film"
                    >
                    @error('titre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Titre original --}}
                <div>
                    <label for="titre_original" class="block text-sm font-medium text-gray-700 mb-2">
                        Titre original (autre langue)
                    </label>
                    <input
                        type="text"
                        id="titre_original"
                        name="titre_original"
                        value="{{ old('titre_original') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('titre_original') border-red-300 @enderror"
                        placeholder="Titre dans la langue originale si différent"
                    >
                    @error('titre_original')
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
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('realisateurs') border-red-300 @enderror"
                        placeholder="Un réalisateur par ligne"
                    >{{ old('realisateurs') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Un nom par ligne</p>
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
                    >{{ old('acteurs_principaux') }}</textarea>
                    @error('acteurs_principaux')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Genre --}}
                <div>
                    <label for="genre" class="block text-sm font-medium text-gray-700 mb-2">
                        Genre principal <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="genre"
                        name="genre[]"
                        required
                        multiple
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('genre') border-red-300 @enderror"
                    >
                        <option value="" disabled">Sélectionnez un genre</option>
                        @foreach(\App\Domain\Enums\GenreFilm::cases() as $genreOption)
                            <option value="{{ $genreOption->value }}" @selected(in_array($genreOption->value ,old('genre',[])))>
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
                        value="{{ old('duree_minutes') }}"
                        required
                        min="1"
                        max="1000"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('duree_minutes') border-red-300 @enderror"
                        placeholder="120"
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
                        <option value="">Sélectionnez une classification</option>
                        @foreach(\App\Domain\Enums\ClassificationFilm::cases() as $classif)
                            <option value="{{ $classif->value }}" {{ old('classification') === $classif->value ? 'selected' : '' }}>
                                {{ $classif->label() }}
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
                        value="{{ old('date_sortie') }}"
                        required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('date_sortie') border-red-300 @enderror"
                    >
                    @error('date_sortie')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Synopsis --}}
            <div class="mt-6">
                <label for="synopsis" class="block text-sm font-medium text-gray-700 mb-2">
                    Synopsis
                </label>
                <textarea
                    id="synopsis"
                    name="synopsis"
                    rows="4"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('synopsis') border-red-300 @enderror"
                    placeholder="Synopsis du film..."
                >{{ old('synopsis') }}</textarea>
                @error('synopsis')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </x-card>

        {{-- Informations techniques --}}
        <x-card theme="admin" variant="shadow" class="bg-white">
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900">Informations techniques</h3>
            </x-slot:header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Pays d'origine --}}
                {{-- <div>
                    <label for="pays_origine" class="block text-sm font-medium text-gray-700 mb-2">
                        Pays d'origine <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="pays_origine"
                        name="pays_origine"
                        value="{{ old('pays_origine', 'France') }}"
                        required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('pays_origine') border-red-300 @enderror"
                        placeholder="France, États-Unis, etc."
                    >
                    @error('pays_origine')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div> --}}

                {{-- Langue originale --}}
                <div>
                    <label for="langue_originale" class="block text-sm font-medium text-gray-700 mb-2">
                        Langue originale <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="langue_originale"
                        name="langue_originale"
                        value="{{ old('langue_originale', 'français') }}"
                        required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('langue_originale') border-red-300 @enderror"
                        placeholder="français, anglais, etc."
                    >
                    @error('langue_originale')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Producteur --}}
                {{-- <div>
                    <label for="producteur" class="block text-sm font-medium text-gray-700 mb-2">
                        Producteur
                    </label>
                    <input
                        type="text"
                        id="producteur"
                        name="producteur"
                        value="{{ old('producteur') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('producteur') border-red-300 @enderror"
                        placeholder="Nom du producteur ou de la société de production"
                    >
                    @error('producteur')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div> --}}

                {{-- URL Affiche --}}
                <div>
                    <label for="affiche_url" class="block text-sm font-medium text-gray-700 mb-2">
                        URL de l'affiche
                    </label>
                    <input
                        type="url"
                        id="affiche_url"
                        name="affiche_url"
                        value="{{ old('affiche_url') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('affiche_url') border-red-300 @enderror"
                        placeholder="https://..."
                    >
                    @error('affiche_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- URL Bande-annonce --}}
                <div>
                    <label for="bande_annonce_url" class="block text-sm font-medium text-gray-700 mb-2">
                        URL de la bande-annonce
                    </label>
                    <input
                        type="url"
                        id="bande_annonce_url"
                        name="bande_annonce_url"
                        value="{{ old('bande_annonce_url') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('bande_annonce_url') border-red-300 @enderror"
                        placeholder="https://..."
                    >
                    @error('bande_annonce_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Note critique --}}
                <div>
                    <label for="note_critique" class="block text-sm font-medium text-gray-700 mb-2">
                        Note critique
                    </label>
                    <input
                        type="number"
                        step="0.1"
                        min="0"
                        max="10"
                        id="note_critique"
                        name="note_critique"
                        value="{{ old('note_critique') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('note_critique') border-red-300 @enderror"
                        placeholder="0 à 10"
                    >
                    @error('note_critique')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Note public --}}
                <div>
                    <label for="note_public" class="block text-sm font-medium text-gray-700 mb-2">
                        Note public
                    </label>
                    <input
                        type="number"
                        step="0.1"
                        min="0"
                        max="10"
                        id="note_public"
                        name="note_public"
                        value="{{ old('note_public') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('note_public') border-red-300 @enderror"
                        placeholder="0 à 10"
                    >
                    @error('note_public')
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
                    {{ old('est_actif', true) ? 'checked' : '' }}
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
                href="{{ route('admin.films.index') }}"
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
                Créer le film
            </x-button>
        </div>
    </form>
@endsection
