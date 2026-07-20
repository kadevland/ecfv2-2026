@extends('layouts.admin')

@section('title', 'Modifier ' . $cinema->nom)

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.cinemas.index') }}" class="hover:text-gray-900">Cinémas</a>
        <span>/</span>
        <a href="{{ route('admin.cinemas.show', $uuid) }}" class="hover:text-gray-900">{{ $cinema->nom }}</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Modifier</span>
    </nav>
@endsection

@section('actions')
    <x-button href="{{ route('admin.cinemas.show', $uuid) }}" variant="outlined" theme="admin" icon="heroicon-o-arrow-left">
        Retour
    </x-button>
@endsection

@section('content')
    {{-- Form globale --}}
    <form method="POST" action="{{ route('admin.cinemas.update', $uuid) }}" id="cinema-form">
        @csrf
        @method('PUT')

        <div class="flex flex-col gap-6">
            {{-- Informations générales --}}
            <x-card theme="admin" title="Informations générales" :subtitle="$cinema->nom" size="md" variant="shadow">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du cinéma <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nom" name="nom"
                            value="{{ old('nom', $cinema->nom) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nom') border-red-500 @enderror"
                            placeholder="Nom du cinéma">
                        @error('nom')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pays" class="block text-sm font-medium text-gray-700 mb-2">
                            Pays <span class="text-red-500">*</span>
                        </label>
                        <select id="pays" name="pays" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pays') border-red-500 @enderror">
                            <option value="FR" {{ old('pays', $cinema->pays) === 'FR' ? 'selected' : '' }}>France
                            </option>
                            <option value="BE" {{ old('pays', $cinema->pays) === 'BE' ? 'selected' : '' }}>Belgique
                            </option>
                        </select>
                        @error('pays')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            {{-- Adresse --}}
            <x-card theme="admin" title="Adresse" size="md" variant="shadow">

                <div>
                    <label for="rue" class="block text-sm font-medium text-gray-700 mb-2">
                        Rue <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="rue" name="rue"
                        value="{{ old('rue', $cinema->rue) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rue') border-red-500 @enderror"
                        placeholder="Numéro et nom de rue">
                    @error('rue')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="ville" class="block text-sm font-medium text-gray-700 mb-2">
                            Ville <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="ville" name="ville"
                            value="{{ old('ville', $cinema->ville) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('ville') border-red-500 @enderror"
                            placeholder="Ville">
                        @error('ville')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="code_postal" class="block text-sm font-medium text-gray-700 mb-2">
                            Code postal <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="code_postal" name="code_postal"
                            value="{{ old('code_postal', $cinema->codePostal) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('code_postal') border-red-500 @enderror"
                            placeholder="Code postal">
                        @error('code_postal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            {{-- Coordonnées GPS --}}
            <x-card theme="admin" title="Coordonnées GPS" size="md" variant="shadow">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                            Latitude <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="any" id="latitude" name="latitude"
                            value="{{ old('latitude', $cinema->latitude) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('latitude') border-red-500 @enderror"
                            placeholder="Ex: 48.8566">
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                            Longitude <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="any" id="longitude" name="longitude"
                            value="{{ old('longitude', $cinema->longitude) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('longitude') border-red-500 @enderror"
                            placeholder="Ex: 2.3522">
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            {{-- Contact --}}
            <x-card theme="admin" title="Contact" size="md" variant="shadow">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone
                        </label>
                        <input type="tel" id="telephone" name="telephone"
                            value="{{ old('telephone', $cinema->telephone) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('telephone') border-red-500 @enderror"
                            placeholder="Ex: +33 1 23 45 67 89">
                        @error('telephone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" id="email" name="email"
                            value="{{ old('email', $cinema->email) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                            placeholder="contact@cinema.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            {{-- Description et Statut --}}
            <x-card theme="admin" title="Description et Statut" size="md" variant="shadow">
                <div class="space-y-4">
                    <div>
                        <x-markdown-editor name="description" label="Description" :value="old('description', $cinema->description)"
                            placeholder="Décrivez votre cinéma..." minHeight="400px"
                            help="Utilisez le Markdown pour formater votre texte. Vous pouvez inclure les services dans la description."
                            autosave="true" />
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="est_actif" name="est_actif" value="1"
                            {{ old('est_actif', $cinema->estActif) ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="est_actif" class="ml-2 block text-sm text-gray-900">
                            Cinéma actif
                        </label>
                    </div>
                </div>
            </x-card>

            {{-- Horaires d'ouverture --}}
            <x-card theme="admin" title="Horaires d'ouverture" size="md" variant="shadow">
                <x-horaires-form :horaires="$cinema->horaires" />
            </x-card>

            @error('general')
                <x-card theme="admin" variant="bordered" class="border-red-200 bg-red-50">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Erreur</h3>
                            <div class="mt-2 text-sm text-red-700">
                                {{ $message }}
                            </div>
                        </div>
                    </div>
                </x-card>
            @enderror

            {{-- Actions --}}
            <x-card theme="admin" variant="shadow">
                <div class="flex justify-end space-x-4">
                    <x-button href="{{ route('admin.cinemas.show', $uuid) }}" variant="outlined" theme="admin">
                        Annuler
                    </x-button>
                    <x-button type="submit" color="primary" theme="admin" icon="heroicon-o-check">
                        Sauvegarder
                    </x-button>
                </div>
            </x-card>
        </div>
    </form>
@endsection
