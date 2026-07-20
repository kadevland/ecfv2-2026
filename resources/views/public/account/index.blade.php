@extends('layouts.cinema')

@section('title', 'Mon Compte')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
            Mon <span class="text-gold">Compte</span>
        </h1>
        <p class="text-gray-300">Gérez vos réservations et informations personnelles</p>
    </div>

    <div x-data="accountApp()" x-init="init()">
        <!-- Navigation Tabs -->
        <div class="mb-8">
            <nav class="flex space-x-1 bg-gray-900 p-1 rounded-xl border border-gray-800">
                {{-- <button @click="activeTab = 'reservations'"
                        :class="activeTab === 'reservations' ? 'bg-gold text-black' : 'text-gray-300 hover:text-white'"
                        class="flex-1 px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                    Mes Réservations
                </button> --}}
                <button @click="activeTab = 'profile'"
                        :class="activeTab === 'profile' ? 'text-cinema-gold hover:text-cinema-gold-light' : 'text-gray-300 hover:text-white'"
                        class="flex-1 px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                    Mon Profil
                </button>
                <button @click="activeTab = 'preferences'"
                        :class="activeTab === 'preferences' ? 'text-cinema-gold hover:text-cinema-gold-light' : 'text-gray-300 hover:text-white'"
                        class="flex-1 px-4 py-3 text-sm font-medium rounded-lg transition-colors">
                    Préférences
                </button>
            </nav>
        </div>

        <!-- Tab Content: Réservations -->
        {{-- <div x-show="activeTab === 'reservations'" class="space-y-6">
            <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white">Mes Réservations</h2>
                    <span class="text-sm text-gray-400">{{ $activeCount }} réservation(s) active(s)</span>
                </div>

                <!-- Lien vers la page dédiée -->
                <div class="text-center py-12">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gold/10 mb-4">
                        <svg class="h-8 w-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a1 1 0 001 1h1a1 1 0 001-1V7a2 2 0 00-2-2H5zM5 14a2 2 0 00-2 2v3a1 1 0 001 1h1a1 1 0 001-1v-3a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">Gérez vos réservations</h3>
                    <p class="text-gray-400 mb-6">Consultez vos réservations, téléchargez vos billets et gérez vos QR codes</p>
                    <a href="{{ route('account.reservations') }}"
                       class="inline-flex items-center px-6 py-3 bg-gold hover:bg-yellow-500 text-black font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a1 1 0 001 1h1a1 1 0 001-1V7a2 2 0 00-2-2H5zM5 14a2 2 0 00-2 2v3a1 1 0 001 1h1a1 1 0 001-1v-3a2 2 0 00-2-2H5z" />
                        </svg>
                        Voir mes réservations
                    </a>
                </div>
            </div>
        </div> --}}

        <!-- Tab Content: Profil -->
        <div x-show="activeTab === 'profile'" class="space-y-6">
            <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
                <h2 class="text-xl font-bold text-white mb-6">Informations personnelles</h2>

                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Prénom</label>
                            <input type="text"
                                   value="{{ $user->prenom ?? 'Non renseigné' }}"
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Nom</label>
                            <input type="text"
                                   value="{{ $user->nom ?? 'Non renseigné' }}"
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <input type="email"
                               value="{{ $user->email }}"
                               readonly
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-gray-300 cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">L'email ne peut pas être modifié</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Téléphone</label>
                        <input type="tel"
                               value="{{ $user->telephone ?? '' }}"
                               placeholder="06 12 34 56 78"
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Date de naissance</label>
                        <input type="date"
                               value="{{ $user->date_naissance ? $user->date_naissance->format('Y-m-d') : '' }}"
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent">
                    </div>

                    @if($user->adresse)
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Adresse</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <input type="text"
                                       value="{{ $user->adresse['rue'] ?? '' }}"
                                       placeholder="Adresse"
                                       class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent">
                            </div>
                            <div>
                                <input type="text"
                                       value="{{ $user->adresse['code_postal'] ?? '' }}"
                                       placeholder="Code postal"
                                       class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent">
                            </div>
                        </div>
                        <div class="mt-2">
                            <input type="text"
                                   value="{{ $user->adresse['ville'] ?? '' }}"
                                   placeholder="Ville"
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent">
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Rôle</label>
                        <input type="text"
                               value="{{ ucfirst(is_string($user->type) ? $user->type : $user->type->value) }}"
                               readonly
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-gray-300 cursor-not-allowed">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-6 py-3  bg-cinema-gold text-black hover:bg-yellow-600 font-medium rounded-lg transition-colors">
                            Sauvegarder les modifications
                        </button>
                    </div>
                </form>
            </div>

            <!-- Changer le mot de passe -->
            <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-6">Changer le mot de passe</h3>

                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Mot de passe actuel</label>
                        <input type="password"
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nouveau mot de passe</label>
                        <input type="password"
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Confirmer le nouveau mot de passe</label>
                        <input type="password"
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                            Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab Content: Préférences -->
        <div x-show="activeTab === 'preferences'" class="space-y-6">
            <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
                <h2 class="text-xl font-bold text-white mb-6">Préférences de notification</h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-medium">Email de confirmation</h3>
                            <p class="text-sm text-gray-400">Recevoir un email après chaque réservation</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gold/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gold"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-medium">Rappels de séance</h3>
                            <p class="text-sm text-gray-400">Recevoir un rappel 2h avant la séance</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gold/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gold"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-medium">Nouveautés et promotions</h3>
                            <p class="text-sm text-gray-400">Recevoir des informations sur les nouveaux films et offres spéciales</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gold/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gold"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Genres préférés -->
            <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-6">Genres préférés</h3>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" checked class="rounded border-gray-700 bg-gray-800 text-gold focus:ring-gold focus:ring-offset-0">
                        <span class="text-gray-300">Action</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-gray-700 bg-gray-800 text-gold focus:ring-gold focus:ring-offset-0">
                        <span class="text-gray-300">Comédie</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" checked class="rounded border-gray-700 bg-gray-800 text-gold focus:ring-gold focus:ring-offset-0">
                        <span class="text-gray-300">Drame</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-gray-700 bg-gray-800 text-gold focus:ring-gold focus:ring-offset-0">
                        <span class="text-gray-300">Horreur</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-gray-700 bg-gray-800 text-gold focus:ring-gold focus:ring-offset-0">
                        <span class="text-gray-300">Romance</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" checked class="rounded border-gray-700 bg-gray-800 text-gold focus:ring-gold focus:ring-offset-0">
                        <span class="text-gray-300">Sci-Fi</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-gray-700 bg-gray-800 text-gold focus:ring-gold focus:ring-offset-0">
                        <span class="text-gray-300">Thriller</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded border-gray-700 bg-gray-800 text-gold focus:ring-gold focus:ring-offset-0">
                        <span class="text-gray-300">Animation</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button class="px-6 py-3 bg-gold hover:bg-yellow-500 text-black font-medium rounded-lg transition-colors">
                    Sauvegarder les préférences
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function accountApp() {
    return {
        activeTab: 'profile',

        init() {
            // Initialize account data
        }
    }
}
</script>
@endpush
