@extends('layouts.cinema')

@section('title', 'Réservation confirmée')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Succès -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 mx-auto mb-4 flex items-center justify-center bg-green-500 rounded-full">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">Réservation confirmée !</h1>
        <p class="text-gray-300">Votre réservation a été créée avec succès</p>
    </div>

    <!-- Détails réservation -->
    <div class="bg-gray-900/95 backdrop-blur-sm rounded-xl p-6 mb-8 border border-gray-800">
        <h2 class="text-xl font-bold text-white mb-4">Détails de votre réservation</h2>

        <div class="space-y-3 text-gray-300">
            <div class="flex justify-between">
                <span>Numéro de réservation:</span>
                <span class="font-bold text-gold">{{ $numero_reservation ?? 'RES-' . strtoupper(substr($reservation_id ?? '', 0, 8)) }}</span>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-4 justify-center">
        <a href="{{ route('home') }}"
           class="px-8 py-3 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-500 rounded-lg font-medium transition-colors">
            Retour à l'accueil
        </a>

        <a href="{{ route('seances.index') }}"
           class="px-8 py-3 bg-gold text-black font-medium rounded-lg hover:bg-yellow-500 transition-colors">
            Nouvelle réservation
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
.text-gold {
    color: #d4af37;
}

.bg-gold {
    background-color: #d4af37;
}
</style>
@endpush