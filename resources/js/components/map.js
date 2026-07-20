/**
 * Composant Map avec Leaflet
 * Initialise automatiquement toutes les cartes avec la classe .hs-leaflet
 * Utilise Leaflet via CDN chargé dans le composant Blade
 */

class MapComponent {
    constructor() {
        this.initMaps();
    }

    initMaps() {
        document.addEventListener('DOMContentLoaded', () => {
            this.waitForLeaflet(() => {
                this.initializeAllMaps();
            });
        });

        // Réinitialiser si du contenu dynamique est ajouté
        document.addEventListener('htmx:afterSwap', () => {
            this.waitForLeaflet(() => {
                this.initializeAllMaps();
            });
        });
    }

    waitForLeaflet(callback) {
        if (typeof L !== 'undefined') {
            callback();
        } else {
            setTimeout(() => this.waitForLeaflet(callback), 100);
        }
    }

    initializeAllMaps() {
        document.querySelectorAll('.hs-leaflet:not(.map-initialized)').forEach(mapElement => {
            this.initializeMap(mapElement);
        });
    }

    initializeMap(mapElement) {
        const latitude = parseFloat(mapElement.dataset.latitude);
        const longitude = parseFloat(mapElement.dataset.longitude);
        const zoom = parseInt(mapElement.dataset.zoom) || 15;
        const title = mapElement.dataset.title || 'Localisation';

        // Vérifier que les coordonnées sont valides
        if (isNaN(latitude) || isNaN(longitude)) {
            this.showErrorState(mapElement);
            return;
        }

        try {
            // Créer la carte basique avec pin
            const map = L.map(mapElement.id).setView([latitude, longitude], zoom);

            // Ajouter les tuiles OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Ajouter le pin
            L.marker([latitude, longitude])
                .addTo(map)
                .bindPopup(title);

            // Marquer comme initialisé
            mapElement.classList.add('map-initialized');

        } catch (error) {
            console.error('Erreur lors de l\'initialisation de la carte:', error);
            this.showErrorState(mapElement);
        }
    }

    showErrorState(mapElement) {
        mapElement.innerHTML = `
            <div class="h-full flex items-center justify-center bg-gray-100 text-gray-500">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 mb-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                    <p>Coordonnées GPS manquantes</p>
                </div>
            </div>
        `;
        mapElement.classList.add('map-initialized');
    }
}

// Auto-initialisation
new MapComponent();

export default MapComponent;