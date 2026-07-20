@extends('layouts.admin')

@section('title', 'Nouvelle Salle')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.salles.index') }}" class="hover:text-gray-900">Salles</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Nouvelle</span>
    </nav>
@endsection

@section('actions')
    <x-button href="{{ route('admin.salles.index') }}" variant="outlined" theme="admin" icon="heroicon-o-arrow-left">
        Retour
    </x-button>
@endsection

@section('content')
    {{-- Form principale --}}
    <form method="POST" action="{{ route('admin.salles.store') }}" id="salle-form" class="sr-only">
        @csrf
    </form>

    <div class="flex flex-col gap-6">
        {{-- Informations générales --}}
        <x-card theme="admin" title="Informations générales" subtitle="Nom et cinéma de la salle" size="md"
            variant="shadow" class="bg-white">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="cinema_uuid" class="block text-sm font-medium text-gray-700 mb-2">
                        Cinéma <span class="text-red-500">*</span>
                    </label>
                    <select id="cinema_uuid" name="cinema_uuid" form="salle-form" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('cinema_uuid') border-red-500 @enderror">
                        <option value="">Sélectionner un cinéma</option>
                        @foreach ($cinemas as $cinema)
                            <option value="{{ $cinema->uuid }}"
                                {{ old('cinema_uuid') === $cinema->uuid ? 'selected' : '' }}>
                                {{ $cinema->nom }} - {{ $cinema->ville }}
                            </option>
                        @endforeach
                    </select>
                    @error('cinema_uuid')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de la salle <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nom" name="nom" form="salle-form" value="{{ old('nom') }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nom') border-red-500 @enderror"
                        placeholder="Ex: Salle 1, Salle Premium, Salle IMAX">
                    @error('nom')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-2">
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <select id="statut" name="statut" form="salle-form" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('statut') border-red-500 @enderror">
                        <option value="ACTIVE" {{ old('statut', 'ACTIVE') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                        <option value="MAINTENANCE" {{ old('statut') === 'MAINTENANCE' ? 'selected' : '' }}>En maintenance
                        </option>
                        <option value="RENOVATION" {{ old('statut') === 'RENOVATION' ? 'selected' : '' }}>En rénovation
                        </option>
                        <option value="HORS_SERVICE" {{ old('statut') === 'HORS_SERVICE' ? 'selected' : '' }}>Hors service
                        </option>
                    </select>
                    @error('statut')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </x-card>

        {{-- Configuration des places --}}
        <x-card theme="admin" title="Configuration des places" subtitle="Capacité et disposition de la salle"
            size="md" variant="shadow" class="bg-white">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="capacite_totale" class="block text-sm font-medium text-gray-700 mb-2">
                        Capacité totale <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="capacite_totale" name="capacite_totale" form="salle-form"
                        value="{{ old('capacite_totale') }}" required min="10" max="1000"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('capacite_totale') border-red-500 @enderror"
                        placeholder="150">
                    @error('capacite_totale')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nombre_rangees" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre de rangées <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="nombre_rangees" name="nombre_rangees" form="salle-form"
                        value="{{ old('nombre_rangees') }}" required min="1" max="50"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nombre_rangees') border-red-500 @enderror"
                        placeholder="10">
                    @error('nombre_rangees')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="places_par_rangee" class="block text-sm font-medium text-gray-700 mb-2">
                        Places par rangée <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="places_par_rangee" name="places_par_rangee" form="salle-form"
                        value="{{ old('places_par_rangee') }}" required min="1" max="50"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('places_par_rangee') border-red-500 @enderror"
                        placeholder="15">
                    @error('places_par_rangee')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="places_standard" class="block text-sm font-medium text-gray-700 mb-2">
                        Places standard
                    </label>
                    <input type="number" id="places_standard" name="places_standard" form="salle-form"
                        value="{{ old('places_standard', 0) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('places_standard') border-red-500 @enderror"
                        placeholder="120">
                    @error('places_standard')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <div>
                    <label for="places_pmr" class="block text-sm font-medium text-gray-700 mb-2">
                        Places PMR
                    </label>
                    <input type="number" id="places_pmr" name="places_pmr" form="salle-form"
                        value="{{ old('places_pmr', 0) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('places_pmr') border-red-500 @enderror"
                        placeholder="5">
                    @error('places_pmr')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-card>

        {{-- Équipements techniques --}}
        <x-card theme="admin" title="Équipements techniques" subtitle="Configuration audio-visuelle" size="md"
            variant="shadow" class="bg-white">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Qualités vidéo
                    </label>
                    <div class="space-y-2 border border-gray-200 rounded-md p-4">
                        <p class="text-xs text-gray-500 mb-3">Qualités de projection disponibles pour cette salle</p>
                        @foreach($qualitesProjection as $qualite)
                            <label class="flex items-center">
                                <input type="checkbox" name="qualite_projection[]" value="{{ $qualite->value }}" form="salle-form"
                                    {{ in_array($qualite->value, old('qualite_projection', [])) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm">{{ $qualite->getLabel() }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('qualite_projection')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Qualités audio
                    </label>
                    <div class="space-y-2 border border-gray-200 rounded-md p-4">
                        <p class="text-xs text-gray-500 mb-3">Qualités sonores disponibles pour cette salle</p>
                        @foreach($qualitesSonore as $qualite)
                            @if($qualite->value !== 'STEREO')
                                <label class="flex items-center">
                                    <input type="checkbox" name="qualite_sonore[]" value="{{ $qualite->value }}" form="salle-form"
                                        {{ in_array($qualite->value, old('qualite_sonore', [])) ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm">{{ $qualite->getLabel() }}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                    @error('qualite_sonore')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div class="flex items-center">
                    <input type="hidden" name="climatisation" value="0" form="salle-form">
                    <input type="checkbox" id="climatisation" name="climatisation" value="1" form="salle-form"
                        {{ old('climatisation', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                    <label for="climatisation" class="ml-2 text-sm font-medium text-gray-700">
                        Climatisation
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="hidden" name="accessibilite_pmr" value="0" form="salle-form">
                    <input type="checkbox" id="accessibilite_pmr" name="accessibilite_pmr" value="1"
                        form="salle-form" {{ old('accessibilite_pmr', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                    <label for="accessibilite_pmr" class="ml-2 text-sm font-medium text-gray-700">
                        Accessibilité PMR
                    </label>
                </div>
            </div>
        </x-card>

        {{-- Actions --}}
        <div class="flex justify-end space-x-4">
            <x-button href="{{ route('admin.salles.index') }}" variant="outlined" theme="admin">
                Annuler
            </x-button>
            <x-button type="submit" form="salle-form" theme="admin">
                Créer la salle
            </x-button>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const capaciteTotale = document.getElementById('capacite_totale');
                const nombreRangees = document.getElementById('nombre_rangees');
                const placesParRangee = document.getElementById('places_par_rangee');
                const placesStandard = document.getElementById('places_standard');
                const placesPmr = document.getElementById('places_pmr');

                function validateCapacity() {
                    const capacite = parseInt(capaciteTotale.value) || 0;
                    const rangees = parseInt(nombreRangees.value) || 0;
                    const parRangee = parseInt(placesParRangee.value) || 0;
                    const standard = parseInt(placesStandard.value) || 0;
                    const pmr = parseInt(placesPmr.value) || 0;

                    const capaciteCalculee = rangees * parRangee;
                    const sommePlaces = standard + pmr;

                    // Supprime les messages d'erreur existants
                    document.querySelectorAll('.capacity-error').forEach(el => el.remove());

                    let hasError = false;

                    // Vérification 1: rangées × places/rangée = capacité totale
                    if (rangees > 0 && parRangee > 0 && capaciteCalculee !== capacite) {
                        showError(capaciteTotale,
                            `Incohérence: ${rangees} × ${parRangee} = ${capaciteCalculee} ≠ ${capacite}`);
                        hasError = true;
                    }

                    // Vérification 2: somme des places par type = capacité totale
                    if (sommePlaces > 0 && sommePlaces !== capacite) {
                        showError(placesStandard, `Incohérence: ${standard} + ${pmr} = ${sommePlaces} ≠ ${capacite}`);
                        hasError = true;
                    }

                    return !hasError;
                }

                function showError(element, message) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'capacity-error mt-1 text-sm text-red-600';
                    errorDiv.textContent = message;
                    element.parentNode.appendChild(errorDiv);
                }

                // Validation en temps réel
                [capaciteTotale, nombreRangees, placesParRangee, placesStandard, placesPmr].forEach(field => {
                    field.addEventListener('input', validateCapacity);
                    field.addEventListener('change', validateCapacity);
                });

                // Validation avant soumission
                document.getElementById('salle-form').addEventListener('submit', function(e) {
                    if (!validateCapacity()) {
                        e.preventDefault();
                        alert(
                            'Veuillez corriger les incohérences dans la configuration des places avant de continuer.');
                    }
                });

            });
        </script>
    @endpush
@endsection
