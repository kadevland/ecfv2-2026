@props([
    'horaires' => null,
])

@php
    $jours = \App\Domain\Shared\Enums\JourSemaine::cases();
@endphp

<div class="space-y-6">
    @foreach ($jours as $jour)
        @php
            $jourValue = $jour->value;
            $horaireJour = $horaires?->getHoraireJour($jour);
            $isOpen = $horaireJour && !$horaireJour->estFerme();
        @endphp
        <div class="border border-gray-200 rounded-lg p-4"
             x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }">

            <div class="flex items-center mb-4">
                <h4 class="text-lg font-medium text-gray-900">{{ $jour->getLabel() }}</h4>
                <div class="ml-auto">
                    <input type="checkbox"
                           id="ouvert_{{ $jourValue }}"
                           name="horaires[{{ $jourValue }}][ouvert]"
                           value="1"
                           x-model="open"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="ouvert_{{ $jourValue }}" class="ml-2 text-sm text-gray-700">Ouvert</label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4"
                 x-show="open"
                 x-transition>

                <div class="space-y-4">
                    <h5 class="font-medium text-gray-700">Matin</h5>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Ouverture</label>
                            <input type="time" name="horaires[{{ $jourValue }}][debut_matin]"
                                value="{{ $horaireJour ? $horaireJour->debutMatin : '09:00' }}"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Fermeture public</label>
                            <input type="time" name="horaires[{{ $jourValue }}][fin_matin]"
                                value="{{ $horaireJour ? $horaireJour->finMatin : '12:30' }}"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Durée max séances (min)</label>
                        <input type="number" name="horaires[{{ $jourValue }}][duree_max_seance_matin]"
                            value="{{ $horaireJour ? $horaireJour->dureeMaxSeanceMatin : 120 }}" min="0"
                            max="480" step="15"
                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Durée max autorisée après fermeture (ex: 120 = 2h)</p>
                    </div>



                </div>
                <div class="space-y-4">
                    <h5 class="font-medium text-gray-700">Après-midi</h5>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Ouverture</label>
                            <input type="time" name="horaires[{{ $jourValue }}][debut_apres]"
                                value="{{ $horaireJour ? $horaireJour->debutApres : '14:00' }}"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Fermeture public</label>
                            <input type="time" name="horaires[{{ $jourValue }}][fin_apres]"
                                value="{{ $horaireJour ? $horaireJour->finApres : '22:00' }}"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Durée max séances (min)</label>
                        <input type="number" name="horaires[{{ $jourValue }}][duree_max_seance_apres]"
                            value="{{ $horaireJour ? $horaireJour->dureeMaxSeanceApres : 120 }}" min="0"
                            max="480" step="15"
                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Durée max autorisée après fermeture (ex: 120 = 2h)</p>
                    </div>

                </div>

            </div>

        </div>
    @endforeach
</div>

