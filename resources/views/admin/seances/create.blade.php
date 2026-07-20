@extends('layouts.admin')

@section('title', 'Créer une Séance')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.seances.index') }}" class="hover:text-gray-900">Séances</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Créer</span>
    </nav>
@endsection

@section('subtitle', 'Planifiez une nouvelle séance de cinéma')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Form Card -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Informations de la séance</h2>
                <p class="text-sm text-gray-600">Remplissez tous les champs pour créer une nouvelle séance</p>
            </div>

            <form method="POST" action="{{ route('admin.seances.store') }}" class="p-6 space-y-6"
                  x-data="seanceForm({{ $film->dureeMinutes }})"
                  x-init="updateCalculatedTimes()">
                @csrf

                <!-- Display validation errors -->
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Des erreurs ont été détectées :</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc space-y-1 pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Film and Salle Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Film Display (NON modifiable) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Film sélectionné
                        </label>
                        <div class="w-full px-3 py-2 border border-gray-200 rounded-md bg-gray-50 text-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $film->titre }}</span>
                                    <span class="text-gray-600 ml-2">({{ $film->dureeMinutes }}min)</span>
                                </div>
                                <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded">Non modifiable</span>
                            </div>
                            @if($film->getClassificationLabel())
                                <div class="mt-1 text-xs text-gray-500">
                                    Classification: <span class="font-medium">{{ $film->getClassificationLabel() }}</span>
                                </div>
                            @endif
                        </div>
                        <!-- Hidden input pour film_id -->
                        <input type="hidden" name="film_id" value="{{ $film->id->value }}" id="film_id">
                    </div>

                    <!-- Salle Selection -->
                    <div>
                        <label for="salle_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Salle <span class="text-red-500">*</span>
                        </label>
                        <select id="salle_id" name="salle_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('salle_id') border-red-300 @enderror">
                            <option value="">Sélectionnez une salle</option>
                            @foreach($salles as $salle)
                                <option value="{{ $salle['id'] }}"
                                        @if(old('salle_id') === $salle['id'] || $preSelectedSalle === $salle['id']) selected @endif>
                                    {{ $salle['display_name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('salle_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Date et Heure Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Date de la séance -->
                    <div>
                        <label for="date_seance" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de la séance <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="date_seance" name="date_seance"
                               x-model="dateSeance"
                               @change="updateCalculatedTimes"
                               value="{{ old('date_seance') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('date_seance') border-red-300 @enderror">
                        @error('date_seance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Heure de début -->
                    <div>
                        <label for="heure_debut" class="block text-sm font-medium text-gray-700 mb-2">
                            Heure de début <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="heure_debut" name="heure_debut"
                               x-model="heureDebut"
                               @change="updateCalculatedTimes"
                               value="{{ old('heure_debut', '20:30') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('heure_debut') border-red-300 @enderror">
                        @error('heure_debut')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Durée additionnelle -->
                    <div>
                        <label for="duree_additionnelle" class="block text-sm font-medium text-gray-700 mb-2">
                            Durée additionnelle <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="duree_additionnelle" name="duree_additionnelle"
                                   x-model.number="dureeAdditionnelle"
                                   @input="updateCalculatedTimes"
                                   value="{{ old('duree_additionnelle', '30') }}" required min="10" max="60"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('duree_additionnelle') border-red-300 @enderror">
                            <span class="absolute right-3 top-2.5 text-sm text-gray-500">min</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Temps entre séances (nettoyage + changement)</p>
                        @error('duree_additionnelle')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Heure de fin calculée -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <span class="text-sm font-medium text-blue-800">Heure de fin calculée :</span>
                            <span x-text="heureFinDisplay" class="text-sm text-blue-700 ml-2 font-mono">--:--</span>
                            <span class="text-xs text-blue-600 ml-2">(Film: {{ $film->dureeMinutes }}min + additionnelle)</span>
                        </div>
                    </div>
                </div>

                <!-- Hidden input pour compatibilité backend -->
                <input type="hidden" name="date_heure_debut" :value="dateHeureDebut">
                <!-- PAS de date_heure_fin - calculée dans le handler -->

                <!-- Version and Options -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Version et Options</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Version -->
                    <div>
                        <label for="version" class="block text-sm font-medium text-gray-700 mb-2">
                            Version <span class="text-red-500">*</span>
                        </label>
                        <select id="version" name="version" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('version') border-red-300 @enderror">
                            <option value="">Sélectionnez une version</option>
                            @foreach($versions as $version)
                                <option value="{{ $version['value'] }}" @if(old('version') === $version['value']) selected @endif>
                                    {{ $version['label'] }} ({{ $version['value'] }})
                                </option>
                            @endforeach
                        </select>
                        @error('version')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut automatique (toujours "programmee" à la création) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Statut initial
                        </label>
                        <div class="w-full px-3 py-2 border border-gray-200 rounded-md bg-gray-50 text-sm">
                            <div class="flex items-center">
                                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <span class="size-1.5 inline-block rounded-full bg-blue-800"></span>
                                    Programmée
                                </span>
                                <span class="text-xs text-gray-500 ml-3">Statut automatique à la création</span>
                            </div>
                        </div>
                        <input type="hidden" name="statut" value="{{ \App\Domain\Enums\StatutSeance::PROGRAMMEE->value }}">
                    </div>
                </div>
                </div>

                <!-- Tarification -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tarification</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @php
                            $tarifCategories = [];
                            foreach(\App\Domain\Enums\TypeTarif::cases() as $tarif) {
                                $tarifCategories[$tarif->value] = [
                                    'label' => $tarif->label(),
                                    'default' => $tarif->calculateDefaultPrice()
                                ];
                            }
                        @endphp
                        @foreach($tarifCategories as $key => $tarif)
                            <div>
                                <label for="tarifs_{{ $key }}" class="block text-xs font-medium text-gray-600 mb-1">
                                    {{ $tarif['label'] }} (€) <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       id="tarifs_{{ $key }}"
                                       name="tarifs[{{ $key }}]"
                                       value="{{ old('tarifs.'.$key, $tarif['default']) }}"
                                       step="0.01"
                                       min="0"
                                       max="999.99"
                                       required
                                       placeholder="{{ $tarif['default'] }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tarifs.'.$key) border-red-300 @enderror">
                                @error('tarifs.'.$key)
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Qualités et Options -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Qualités et Options</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Qualité Projection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Qualité de projection</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input id="projection_standard" name="qualite_projection" type="radio" value=""
                                           @if(old('qualite_projection') === '' || old('qualite_projection') === null) checked @endif
                                           class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <label for="projection_standard" class="ml-3 text-sm text-gray-700">Standard</label>
                                </div>
                                @foreach($qualitesProjection as $qualite)
                                    <div class="flex items-center">
                                        <input id="projection_{{ $qualite->value }}" name="qualite_projection" type="radio" value="{{ $qualite->value }}"
                                               @if(old('qualite_projection') === $qualite->value) checked @endif
                                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <label for="projection_{{ $qualite->value }}" class="ml-3 text-sm text-gray-700">{{ $qualite->getLabel() }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Qualité Sonore -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Qualité sonore</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input id="sonore_standard" name="qualite_sonore" type="radio" value=""
                                           @if(old('qualite_sonore') === '' || old('qualite_sonore') === null) checked @endif
                                           class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <label for="sonore_standard" class="ml-3 text-sm text-gray-700">Standard</label>
                                </div>
                                @foreach($qualitesSonores as $qualite)
                                    <div class="flex items-center">
                                        <input id="sonore_{{ $qualite->value }}" name="qualite_sonore" type="radio" value="{{ $qualite->value }}"
                                               @if(old('qualite_sonore') === $qualite->value) checked @endif
                                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <label for="sonore_{{ $qualite->value }}" class="ml-3 text-sm text-gray-700">{{ $qualite->getLabel() }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Options supplémentaires -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Options supplémentaires</label>
                            <div class="space-y-2">
                                <!-- Placement Libre -->
                                <div class="flex items-center">
                                    <input id="placement_libre" name="placement_libre" type="checkbox" value="1"
                                           @if(old('placement_libre')) checked @endif
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="placement_libre" class="ml-3 text-sm text-gray-700">
                                        Placement libre
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Pas de numérotation des sièges</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <a href="{{ route('admin.seances.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Créer la séance
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- AlpineJS for dynamic form behavior -->
    <script>
        function seanceForm(filmDuration) {
            return {
                dateSeance: '{{ old("date_seance") }}',
                heureDebut: '{{ old("heure_debut", "20:30") }}',
                dureeAdditionnelle: {{ old('duree_additionnelle', 30) }},
                heureFinDisplay: '--:--',
                dateHeureDebut: '',
                dateHeureFin: '',

                updateCalculatedTimes() {
                    if (this.dateSeance && this.heureDebut) {
                        // Créer la datetime de début
                        const dateTimeDebut = new Date(`${this.dateSeance}T${this.heureDebut}`);

                        // Calculer la datetime de fin (film + durée additionnelle)
                        const totalDuree = filmDuration + this.dureeAdditionnelle;
                        const dateTimeFin = new Date(dateTimeDebut.getTime() + totalDuree * 60000);

                        // Afficher l'heure de fin calculée
                        this.heureFinDisplay = dateTimeFin.toLocaleTimeString('fr-FR', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        // Remplir les propriétés pour les inputs cachés
                        this.dateHeureDebut = this.formatDateTime(dateTimeDebut);
                        this.dateHeureFin = this.formatDateTime(dateTimeFin);
                    } else {
                        this.heureFinDisplay = '--:--';
                        this.dateHeureDebut = '';
                        this.dateHeureFin = '';
                    }
                },

                formatDateTime(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    return `${year}-${month}-${day}T${hours}:${minutes}`;
                }
            }
        }
    </script>
@endsection
