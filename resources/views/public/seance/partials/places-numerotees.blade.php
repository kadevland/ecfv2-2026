<!-- Écran -->
<div class="text-center mb-8">
    <div class="inline-block bg-gradient-to-r from-gold to-yellow-500 text-black px-8 py-2 rounded-lg font-semibold mb-2">
        ÉCRAN
    </div>
</div>

<!-- Légende -->
<div class="flex justify-center gap-6 mb-8 text-sm">
    <div class="flex items-center gap-2">
        <div class="w-4 h-4 bg-gray-600 rounded"></div>
        <span class="text-gray-300">Libre</span>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-4 h-4 bg-gold rounded"></div>
        <span class="text-gray-300">Sélectionnée</span>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-4 h-4 bg-red-600 rounded"></div>
        <span class="text-gray-300">Occupée</span>
    </div>
</div>

<!-- Plan de salle réaliste -->
<div class="space-y-3">
    @php
        $seatConfig = [
            ['seats' => 8, 'occupied' => ['A3', 'A4']],
            ['seats' => 10, 'occupied' => ['B2', 'B9']],
            ['seats' => 12, 'occupied' => ['C5', 'C6']],
            ['seats' => 14, 'occupied' => ['D8', 'D9']],
            ['seats' => 16, 'occupied' => ['E7', 'E8']],
        ];
        $occupiedSeats = $seance['places_occupees'] ?? [];
    @endphp

    @foreach($seatConfig as $rowIndex => $config)
        @php $rowLetter = chr(65 + $rowIndex); @endphp
        <div class="flex justify-center items-center gap-2">
            <!-- Lettre de rangée (gauche) -->
            <div class="w-8 text-center text-gold font-bold">{{ $rowLetter }}</div>

            <!-- Places -->
            <div class="flex gap-1">
                @for($seatIndex = 1; $seatIndex <= $config['seats']; $seatIndex++)
                    @php
                        $seatLabel = $rowLetter . $seatIndex;
                        $isOccupied = in_array($seatLabel, $occupiedSeats) || in_array($seatLabel, $config['occupied']);
                    @endphp
                    <div class="relative">
                        <input type="checkbox"
                               name="seats[]"
                               value="{{ $seatLabel }}"
                               id="seat-{{ $seatLabel }}"
                               @if($isOccupied) disabled @endif
                               @change="updateSelectedSeats()"
                               class="peer sr-only">
                        <label for="seat-{{ $seatLabel }}"
                               class="block w-8 h-8 rounded cursor-pointer transition-all duration-200 flex items-center justify-center text-sm font-medium
                                      @if($isOccupied)
                                          bg-red-600 cursor-not-allowed text-white
                                      @else
                                          bg-gray-600 hover:bg-gray-500 text-white peer-checked:bg-gold peer-checked:text-black peer-checked:scale-110 peer-checked:shadow-lg
                                      @endif">
                            {{ $seatIndex }}
                        </label>
                    </div>
                @endfor
            </div>

            <!-- Lettre de rangée (droite) -->
            <div class="w-8 text-center text-gold font-bold">{{ $rowLetter }}</div>
        </div>
    @endforeach
</div>