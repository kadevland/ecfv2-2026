@extends('layouts.employee')

@section('title', 'Déclarer un incident')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    ⚠️ Déclarer un nouvel incident
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('employee.incidents.store') }}" class="space-y-6">
                    @csrf

                    <!-- Type d'incident -->
                    <div>
                        <label for="type_incident" class="block text-sm font-medium text-gray-700 mb-2">
                            Type d'incident <span class="text-red-500">*</span>
                        </label>
                        <select name="type_incident" id="type_incident"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('type_incident') border-red-300 @enderror">
                            <option value="">Sélectionner un type</option>
                            @foreach($typesDisponibles as $key => $label)
                                <option value="{{ $key }}" @selected(old('type_incident') == $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_incident')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sévérité -->
                    <div>
                        <label for="severite" class="block text-sm font-medium text-gray-700 mb-2">
                            Sévérité <span class="text-red-500">*</span>
                        </label>
                        <select name="severite" id="severite"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('severite') border-red-300 @enderror">
                            <option value="">Sélectionner la sévérité</option>
                            @foreach($severiteDisponibles as $key => $label)
                                <option value="{{ $key }}" @selected(old('severite') == $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('severite')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Salle (optionnel) -->
                    <div>
                        <label for="salle_db_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Salle concernée (optionnel)
                        </label>
                        <select name="salle_db_id" id="salle_db_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('salle_db_id') border-red-300 @enderror">
                            <option value="">Aucune salle spécifique</option>
                            @foreach($sallesDisponibles as $id => $nom)
                                <option value="{{ $id }}" @selected(old('salle_db_id') == $id)>
                                    {{ $nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('salle_db_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Titre -->
                    <div>
                        <label for="titre" class="block text-sm font-medium text-gray-700 mb-2">
                            Titre de l'incident <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="titre" id="titre" value="{{ old('titre') }}"
                               placeholder="Résumé court de l'incident..."
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('titre') border-red-300 @enderror">
                        @error('titre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description détaillée <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="6"
                                  placeholder="Décrivez l'incident en détail : que s'est-il passé, quand, où, circonstances..."
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('employee.incidents.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Annuler
                        </a>
                        <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            🚨 Déclarer l'incident
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Aide -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Conseils pour bien remplir le formulaire
                    </h3>
                    <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                        <li><strong>Critique/Urgence</strong> : Incident bloquant l'exploitation (panne projection, sécurité...)</li>
                        <li><strong>Majeur</strong> : Incident impactant significativement le service</li>
                        <li><strong>Modéré</strong> : Problème gênant mais non bloquant</li>
                        <li><strong>Mineur</strong> : Problème mineur, amélioration</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection