@extends('layouts.cinema')

@section('title', 'Connexion')

@section('content')
<div class="min-h-screen bg-black flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <div class="w-12 h-12 bg-cinema-gold rounded-lg flex items-center justify-center">
                    <span class="text-black font-bold text-xl">🎬</span>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-cinema-gold">
                Connexion à Cinéphoria
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Accédez à votre compte pour réserver vos séances
            </p>
        </div>

        <div class="bg-gray-900 rounded-lg shadow-xl border border-gray-800 p-8">
            @if(session('error'))
                <div class="mb-6 bg-red-900/20 border border-red-700 text-red-400 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">
                        Adresse e-mail
                    </label>
                    <input id="email"
                           name="email"
                           type="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-cinema-gold focus:border-cinema-gold focus:z-10 sm:text-sm"
                           placeholder="votre@email.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">
                        Mot de passe
                    </label>
                    <input id="password"
                           name="password"
                           type="password"
                           required
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-cinema-gold focus:border-cinema-gold focus:z-10 sm:text-sm"
                           placeholder="Votre mot de passe">
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Se souvenir de moi -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember"
                               name="remember"
                               type="checkbox"
                               class="focus:ring-cinema-gold h-4 w-4 text-cinema-gold border-gray-700 bg-gray-800 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-300">
                            Se souvenir de moi
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="text-cinema-gold hover:text-cinema-gold/80">
                            Mot de passe oublié ?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-black bg-cinema-gold hover:bg-cinema-gold/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cinema-gold focus:ring-offset-gray-900 transition-colors">
                        Se connecter
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-400">
                        Pas encore de compte ?
                        <a href="{{ route('register') }}" class="text-cinema-gold hover:text-cinema-gold/80 font-medium">
                            S'inscrire
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection