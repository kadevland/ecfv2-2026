@props([
    'horaires' => null,
    'form' => null,
])

@php
    $jours = \App\Domain\Shared\Enums\JourSemaine::cases();
@endphp

<div class="space-y-6">
    @foreach ($jours as $jour)
        @php
            $jourValue = $jour->value;
            $horaireJour = $horaires?->getHoraireJour($jour);
        @endphp

        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center mb-4">
                <h4 class="text-lg font-medium text-gray-900">{{ $jour->getLabel() }}</h4>
                <div class="ml-auto">
                    <input type="checkbox" id="ouvert_{{ $jourValue }}" name="horaires[{{ $jourValue }}][ouvert]"
                        value="1" {{ $horaireJour && !$horaireJour->estFerme() ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="ouvert_{{ $jourValue }}" class="ml-2 text-sm text-gray-700">Ouvert</label>
                </div>
            </div>

            <div class="horaires-fields grid grid-cols-1 md:grid-cols-2 gap-4"style="{{ $horaireJour && !$horaireJour->estFerme() ? '' : 'display: none;' }}">

                {{-- Horaires matin --}}
                <div class="space-y-4">
                    <h5 class="font-medium text-gray-700">Matin</h5>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Ouverture</label>
                            <input type="time" name="horaires[{{ $jourValue }}][debut_matin]"
                                value="{{ $horaireJour ? $horaireJour->debutMatin : '14:00' }}"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Fermeture public</label>
                            <input type="time" name="horaires[{{ $jourValue }}][fin_matin]"
                                value="{{ $horaireJour ? $horaireJour->finMatin : '18:00' }}"
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

                {{-- Horaires après-midi --}}
                <div class="space-y-4">
                    <div class="flex items-center">
                        <h5 class="font-medium text-gray-700">Après-midi</h5>
                        <div class="ml-auto">
                            <input type="checkbox" id="pause_{{ $jourValue }}"
                                name="horaires[{{ $jourValue }}][avec_pause]" value="1"
                                {{ $horaireJour && !$horaireJour->journeeComplete() ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="pause_{{ $jourValue }}" class="ml-2 text-xs text-gray-700">Avec
                                pause</label>
                        </div>
                    </div>

                    <div class="pause-fields grid grid-cols-2 gap-2"
                        style="{{ $horaireJour && !$horaireJour->journeeComplete() ? '' : 'display: none;' }}">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Réouverture</label>
                            <input type="time" name="horaires[{{ $jourValue }}][debut_apres]"
                                value="{{ $horaireJour?->debutApres ?? '20:00' }}"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Fermeture public</label>
                            <input type="time" name="horaires[{{ $jourValue }}][fin_apres]"
                                value="{{ $horaireJour?->finApres ?? '23:30' }}"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="pause-fields"
                        style="{{ $horaireJour && !$horaireJour->journeeComplete() ? '' : 'display: none;' }}">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Durée max séances (min)</label>
                        <input type="number" name="horaires[{{ $jourValue }}][duree_max_seance_apres]"
                            value="{{ $horaireJour?->dureeMaxSeanceApres ?? 120 }}" min="0" max="480"
                            step="15"
                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Durée max autorisée après fermeture (ex: 120 = 2h)</p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion de l'affichage des champs horaires
            document.querySelectorAll('input[name*="[ouvert]"]').forEach(checkbox => {
                const jourValue = checkbox.name.match(/\[(.*?)\]/)[1];
                const horaireFields = checkbox.closest('.border').querySelector('.horaires-fields');

                checkbox.addEventListener('change', function() {
                    horaireFields.style.display = this.checked ? '' : 'none';
                });
            });

            // Gestion des pauses
            document.querySelectorAll('input[name*="[avec_pause]"]').forEach(checkbox => {
                const jourValue = checkbox.name.match(/\[(.*?)\]/)[1];
                const pauseFields = checkbox.closest('.border').querySelectorAll('.pause-fields');

                checkbox.addEventListener('change', function() {
                    pauseFields.forEach(field => {
                        field.style.display = this.checked ? '' : 'none';
                    });
                });
            });
        });
    </script>
@endpush

