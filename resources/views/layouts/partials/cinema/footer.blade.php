<footer class="border-t border-cinema-gold/10 bg-cinema-black/95 backdrop-blur-sm">
    <x-container class="py-12">
        <x-grid :cols="['default'=>2,'md'=>4]" gap="8" class="mb-8">
            <!-- Cinéphoria Info -->
            <x-grid-item>
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-cinema-gold rounded-lg flex items-center justify-center">
                            <x-brand-logo size="sm" color="black" />
                        </div>
                        <span class="text-xl font-bold font-serif text-cinema-gold">Cinéphoria</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Votre cinéma de référence pour vivre une expérience cinématographique unique.
                        Découvrez les derniers films dans nos salles équipées des technologies les plus récentes.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-cinema-gold transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-cinema-gold transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </x-grid-item>

            <!-- Films -->
            <x-grid-item>
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-cinema-gold">Films</h3>
                    <div class="space-y-2">
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            À l'affiche
                        </x-link>
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Prochainement
                        </x-link>
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Avant-premières
                        </x-link>
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Reprises
                        </x-link>
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Documentaires
                        </x-link>
                    </div>
                </div>
            </x-grid-item>

            <!-- Cinémas -->
            <x-grid-item>
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-cinema-gold">Nos Cinémas</h3>
                    <div class="space-y-2">
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Paris Champs-Élysées
                        </x-link>
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Lyon Part-Dieu
                        </x-link>
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Marseille Vieux-Port
                        </x-link>
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Toulouse Centre
                        </x-link>
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Tous nos cinémas
                        </x-link>
                    </div>
                </div>
            </x-grid-item>

            <!-- Contact & Services -->
            <x-grid-item>
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-cinema-gold">Contact & Services</h3>
                    <div class="space-y-2">
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Nous contacter
                        </x-link>
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            FAQ
                        </x-link>
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Cartes cadeaux
                        </x-link>
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Programme fidélité
                        </x-link>
                        <x-link href="#" theme="cinema" color="gray-400" class="block hover:text-cinema-gold transition-colors">
                            Privatisation salles
                        </x-link>
                    </div>

                    {{-- <!-- Newsletter -->
                    <div class="mt-6 space-y-3">
                        <h4 class="text-sm font-medium text-white">Newsletter</h4>
                        <div class="flex">
                            <input type="email"
                                   placeholder="votre@email.com"
                                   class="flex-1 bg-gray-900/50 border border-gray-700 rounded-l-lg px-3 py-2 text-sm text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cinema-gold focus:border-transparent">
                            <x-button variant="solid" color="primary" theme="cinema" size="sm" class="rounded-l-none">
                                OK
                            </x-button>
                        </div>
                    </div> --}}
                </div>
            </x-grid-item>
        </x-grid>

        <x-divider theme="cinema" color="cinema-gold" class="opacity-20" />

        <!-- Bottom Footer -->
        <div class="flex flex-col md:flex-row justify-between items-center pt-8 space-y-4 md:space-y-0">
            <div class="flex flex-wrap gap-6 text-sm text-gray-400">
                <x-link href="#" theme="cinema" color="gray-400" class="hover:text-cinema-gold transition-colors">
                    Mentions légales
                </x-link>
                <x-link href="#" theme="cinema" color="gray-400" class="hover:text-cinema-gold transition-colors">
                    Politique de confidentialité
                </x-link>
                <x-link href="#" theme="cinema" color="gray-400" class="hover:text-cinema-gold transition-colors">
                    CGV
                </x-link>
                <x-link href="#" theme="cinema" color="gray-400" class="hover:text-cinema-gold transition-colors">
                    Cookies
                </x-link>
                <x-link href="#" theme="cinema" color="gray-400" class="hover:text-cinema-gold transition-colors">
                    Accessibilité
                </x-link>
            </div>

            <div class="text-sm text-gray-400">
                © {{ date('Y') }} Cinéphoria. Tous droits réservés.
            </div>
        </div>
    </x-container>
</footer>
