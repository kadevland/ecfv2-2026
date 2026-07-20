@extends('layouts.cinema')

@section('title', 'Inscription')

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
                Rejoignez Cinéphoria
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Créez votre compte pour réserver vos séances
            </p>
        </div>

        <div class="bg-gray-900 rounded-lg shadow-xl border border-gray-800 p-8">
            @if(session('error'))
                <div class="mb-6 bg-red-900/20 border border-red-700 text-red-400 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Prénom -->
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-300">
                        Prénom
                    </label>
                    <input id="prenom"
                           name="prenom"
                           type="text"
                           value="{{ old('prenom') }}"
                           required
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-cinema-gold focus:border-cinema-gold focus:z-10 sm:text-sm"
                           placeholder="Votre prénom">
                    @error('prenom')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nom -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-300">
                        Nom de famille
                    </label>
                    <input id="nom"
                           name="nom"
                           type="text"
                           value="{{ old('nom') }}"
                           required
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-cinema-gold focus:border-cinema-gold focus:z-10 sm:text-sm"
                           placeholder="Votre nom">
                    @error('nom')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

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
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-cinema-gold focus:border-cinema-gold focus:z-10 sm:text-sm"
                           placeholder="votre@email.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Téléphone -->
                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-300">
                        Téléphone <span class="text-gray-500">(optionnel)</span>
                    </label>
                    <input id="telephone"
                           name="telephone"
                           type="tel"
                           value="{{ old('telephone') }}"
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-cinema-gold focus:border-cinema-gold focus:z-10 sm:text-sm"
                           placeholder="06 12 34 56 78">
                    @error('telephone')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de naissance -->
                <div>
                    <label for="date_naissance" class="block text-sm font-medium text-gray-300">
                        Date de naissance
                    </label>
                    <input id="date_naissance"
                           name="date_naissance"
                           type="date"
                           value="{{ old('date_naissance') }}"
                           required
                           max="{{ now()->subYears(13)->format('Y-m-d') }}"
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-cinema-gold focus:border-cinema-gold focus:z-10 sm:text-sm">
                    @error('date_naissance')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Vous devez avoir au moins 13 ans</p>
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
                           minlength="12"
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-cinema-gold focus:border-cinema-gold focus:z-10 sm:text-sm"
                           placeholder="Votre mot de passe">
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror

                    <!-- Recommandations CNIL avec validation temps réel -->
                    <div class="mt-2 text-xs">
                        <p class="font-medium text-gray-300 mb-2">Votre mot de passe doit contenir :</p>
                        <ul id="password-requirements" class="space-y-1 ml-2">
                            <li id="req-length" class="text-gray-400">
                                <span class="requirement-icon">•</span> Au moins 12 caractères
                            </li>
                            <li id="req-uppercase" class="text-gray-400">
                                <span class="requirement-icon">•</span> Au moins 1 majuscule (A-Z)
                            </li>
                            <li id="req-lowercase" class="text-gray-400">
                                <span class="requirement-icon">•</span> Au moins 1 minuscule (a-z)
                            </li>
                            <li id="req-number" class="text-gray-400">
                                <span class="requirement-icon">•</span> Au moins 1 chiffre (0-9)
                            </li>
                            <li id="req-special" class="text-gray-400">
                                <span class="requirement-icon">•</span> Au moins 1 caractère spécial (!@#$%^&*)
                            </li>
                            <li id="req-entropy" class="text-gray-400">
                                <span class="requirement-icon">•</span> Au moins 80 bits d'entropie
                            </li>
                        </ul>

                        <!-- Indicateur d'entropie -->
                        <div class="mt-3">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-gray-300 text-xs font-medium">Force du mot de passe</span>
                                <span id="entropy-value" class="text-gray-400 text-xs">0 bits</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div id="strength-bar" class="bg-red-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Messages d'erreur pour motifs interdits -->
                        <div id="pattern-errors" class="mt-2 space-y-1"></div>
                    </div>
                </div>

                <!-- Confirmation mot de passe -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300">
                        Confirmer le mot de passe
                    </label>
                    <input id="password_confirmation"
                           name="password_confirmation"
                           type="password"
                           required
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-700 bg-gray-800 text-white placeholder-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-cinema-gold focus:border-cinema-gold focus:z-10 sm:text-sm"
                           placeholder="Confirmez votre mot de passe">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Conditions générales -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms"
                               name="terms"
                               type="checkbox"
                               required
                               class="focus:ring-cinema-gold h-4 w-4 text-cinema-gold border-gray-700 bg-gray-800 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="text-gray-300">
                            J'accepte les
                            <a href="#" class="text-cinema-gold hover:text-cinema-gold/80 underline">conditions générales</a>
                            et la
                            <a href="#" class="text-cinema-gold hover:text-cinema-gold/80 underline">politique de confidentialité</a>
                        </label>
                    </div>
                </div>

                <!-- Newsletter -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="newsletter"
                               name="newsletter"
                               type="checkbox"
                               value="1"
                               class="focus:ring-cinema-gold h-4 w-4 text-cinema-gold border-gray-700 bg-gray-800 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="newsletter" class="text-gray-300">
                            Je souhaite recevoir les actualités et offres spéciales
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-black bg-cinema-gold hover:bg-cinema-gold/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cinema-gold focus:ring-offset-gray-900 transition-colors">
                        Créer mon compte
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-400">
                        Déjà un compte ?
                        <a href="{{ route('login') }}" class="text-cinema-gold hover:text-cinema-gold/80 font-medium">
                            Se connecter
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const strengthBar = document.getElementById('strength-bar');
    const entropyValue = document.getElementById('entropy-value');
    const patternErrors = document.getElementById('pattern-errors');

    // Éléments des critères
    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqLowercase = document.getElementById('req-lowercase');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');
    const reqEntropy = document.getElementById('req-entropy');

    // Motifs CNIL interdits
    const forbiddenPatterns = [
        'password', 'motdepasse', 'admin', 'test', 'user', 'azerty', 'qwerty',
        'login', 'root', 'guest', 'demo', 'sample', 'exemple'
    ];

    // Séquences interdites
    const numericSequences = ['123', '234', '345', '456', '567', '678', '789', '890', '987', '876', '765', '654', '543', '432', '321'];
    const alphaSequences = ['abc', 'bcd', 'cde', 'def', 'efg', 'fgh', 'ghi', 'hij', 'xyz', 'wxy', 'vwx', 'uvw', 'tuv', 'stu', 'rst'];
    const keyboardSequences = ['qwe', 'wer', 'ert', 'rty', 'tyu', 'yui', 'uio', 'iop', 'asd', 'sdf', 'dfg', 'fgh', 'ghj', 'hjk', 'jkl', 'zxc', 'xcv', 'cvb', 'vbn', 'bnm'];

    function calculateEntropy(password) {
        if (!password) return 0;

        let charset = 0;
        if (/[a-z]/.test(password)) charset += 26;
        if (/[A-Z]/.test(password)) charset += 26;
        if (/[0-9]/.test(password)) charset += 10;
        if (/[^a-zA-Z0-9]/.test(password)) charset += 32;

        return Math.floor(Math.log2(Math.pow(charset, password.length)));
    }

    function updateRequirementStatus(element, isValid) {
        const icon = element.querySelector('.requirement-icon');
        if (isValid) {
            element.classList.remove('text-gray-400', 'text-red-400');
            element.classList.add('text-green-400');
            icon.textContent = '✓';
        } else {
            element.classList.remove('text-gray-400', 'text-green-400');
            element.classList.add('text-red-400');
            icon.textContent = '✗';
        }
    }

    function resetRequirement(element) {
        element.classList.remove('text-green-400', 'text-red-400');
        element.classList.add('text-gray-400');
        element.querySelector('.requirement-icon').textContent = '•';
    }

    function validatePasswordRealtime() {
        const password = passwordInput.value;

        // Reset si pas de mot de passe
        if (!password) {
            [reqLength, reqUppercase, reqLowercase, reqNumber, reqSpecial, reqEntropy].forEach(resetRequirement);
            strengthBar.style.width = '0%';
            strengthBar.className = 'bg-red-500 h-2 rounded-full transition-all duration-300';
            entropyValue.textContent = '0 bits';
            patternErrors.innerHTML = '';
            return false;
        }

        // Validation des critères
        const minLength = password.length >= 12;
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(password);

        // Calcul entropie
        const entropy = calculateEntropy(password);
        const hasEntropy = entropy >= 80;

        // Mise à jour visuelle des critères
        updateRequirementStatus(reqLength, minLength);
        updateRequirementStatus(reqUppercase, hasUpper);
        updateRequirementStatus(reqLowercase, hasLower);
        updateRequirementStatus(reqNumber, hasNumber);
        updateRequirementStatus(reqSpecial, hasSpecial);
        updateRequirementStatus(reqEntropy, hasEntropy);

        // Mise à jour de la barre de force
        entropyValue.textContent = `${entropy} bits`;

        let strengthPercentage = 0;
        let strengthClass = 'bg-red-500';

        if (entropy >= 80) {
            strengthPercentage = 100;
            strengthClass = 'bg-green-500';
        } else if (entropy >= 60) {
            strengthPercentage = 75;
            strengthClass = 'bg-yellow-500';
        } else if (entropy >= 40) {
            strengthPercentage = 50;
            strengthClass = 'bg-orange-500';
        } else if (entropy >= 20) {
            strengthPercentage = 25;
            strengthClass = 'bg-red-500';
        }

        strengthBar.style.width = `${strengthPercentage}%`;
        strengthBar.className = `${strengthClass} h-2 rounded-full transition-all duration-300`;

        // Vérification des motifs interdits
        const lowerPassword = password.toLowerCase();
        let patternWarnings = [];

        // Motifs interdits
        for (const pattern of forbiddenPatterns) {
            if (lowerPassword.includes(pattern)) {
                patternWarnings.push(`Évitez d'utiliser "${pattern}"`);
            }
        }

        // Séquences interdites
        for (const seq of [...numericSequences, ...alphaSequences, ...keyboardSequences]) {
            if (lowerPassword.includes(seq)) {
                patternWarnings.push(`Séquence "${seq}" détectée`);
                break;
            }
        }

        // Répétitions
        const repetitionMatch = password.match(/(.)\1{2,}/);
        if (repetitionMatch) {
            patternWarnings.push(`Répétition excessive: "${repetitionMatch[0]}"`);
        }

        // Affichage des avertissements
        patternErrors.innerHTML = '';
        if (patternWarnings.length > 0) {
            patternWarnings.forEach(warning => {
                const div = document.createElement('div');
                div.className = 'text-yellow-400 text-xs';
                div.innerHTML = `⚠ ${warning}`;
                patternErrors.appendChild(div);
            });
        }

        // Retour du résultat complet
        const allCriteriaValid = minLength && hasUpper && hasLower && hasNumber && hasSpecial && hasEntropy;
        const noPatternIssues = patternWarnings.length === 0;

        return allCriteriaValid && noPatternIssues;
    }

    function validateConfirmation() {
        const password = passwordInput.value;
        const confirmation = confirmInput.value;

        if (confirmation && password !== confirmation) {
            confirmInput.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            confirmInput.setCustomValidity('');
        }
    }

    // Événements
    passwordInput.addEventListener('input', function() {
        validatePasswordRealtime();
        validateConfirmation();
    });

    confirmInput.addEventListener('input', validateConfirmation);

    // Validation initiale si le champ contient déjà une valeur
    if (passwordInput.value) {
        validatePasswordRealtime();
    }
});
</script>
@endpush
@endsection