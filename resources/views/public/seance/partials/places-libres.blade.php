<!-- Placement libre avec input number Preline -->
<div class="bg-gray-800/30 rounded-lg p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4 text-gold">Nombre de places</h3>

    <div class="max-w-xs">
        <div class="flex items-center">
            <button type="button"
                    @click="numberOfSeats = Math.max(1, numberOfSeats - 1)"
                    class="size-10 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-s-lg border border-gray-600 bg-gray-800 text-gray-300 hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none"
                    :disabled="numberOfSeats <= 1">
                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                </svg>
            </button>

            <input class="p-0 w-20 bg-transparent border-0 text-gray-300 text-center focus:ring-0 [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                   style="-moz-appearance: textfield;"
                   type="number"
                   name="nombre_places"
                   x-model.number="numberOfSeats"
                   min="1"
                   :max="maxSeats"
                   readonly>

            <button type="button"
                    @click="numberOfSeats = Math.min(maxSeats, numberOfSeats + 1)"
                    class="size-10 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-e-lg border border-gray-600 bg-gray-800 text-gray-300 hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none"
                    :disabled="numberOfSeats >= maxSeats">
                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
            </button>
        </div>

        <div class="border border-gray-600 bg-gray-800"></div>
    </div>

    <p class="text-gray-400 text-sm mt-3">
        Places disponibles: <span class="text-gold font-medium">{{ $seance['places_disponibles'] }}</span>
    </p>
</div>