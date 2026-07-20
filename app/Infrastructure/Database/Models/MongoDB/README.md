# Modèles MongoDB - Read-Side CQRS

Ce dossier contient les modèles Eloquent MongoDB pour le read-side de l'architecture CQRS.

## 📋 Vue d'ensemble

Dans notre architecture CQRS :
- **Write-Side** : PostgreSQL avec entités Domain et repositories
- **Read-Side** : MongoDB avec modèles Eloquent optimisés pour les requêtes

## 🗂️ Modèles disponibles

### CinemaPublic
- **Collection** : `cinema_publics`
- **Usage** : Liste publique des cinémas et détails
- **Scopes** : `actif()`, `byLocation()`
- **Méthodes** : `hasAccessibilityPmr()`, `getFormattedHoraires()`

### FilmCatalogue
- **Collection** : `films_catalogue`
- **Usage** : Catalogue public des films
- **Scopes** : `actif()`, `enExploitation()`, `byGenre()`, `byClassification()`
- **Méthodes** : `getFormattedDuration()`, `isInTheaters()`

### ReservationRead
- **Collection** : `reservations`
- **Usage** : Consultation des réservations
- **Scopes** : `byClient()`, `byCinema()`, `confirmees()`, `futures()`, `passees()`
- **Méthodes** : `getNombrePlaces()`, `canBeCancelled()`, `getFormattedPlaces()`

## 🔧 Configuration

### Connexion MongoDB
```php
// config/database.php
'mongodb' => [
    'driver' => 'mongodb',
    'host' => env('MONGODB_HOST', 'localhost'),
    'port' => env('MONGODB_PORT', 27017),
    'database' => env('MONGODB_DATABASE', 'cinephoria_read'),
    'username' => env('MONGODB_USERNAME'),
    'password' => env('MONGODB_PASSWORD'),
    'options' => [
        'authSource' => env('MONGODB_AUTH_DATABASE', 'admin'),
    ],
],
```

### Variables d'environnement
```bash
MONGODB_HOST=cinephoria.mongodb
MONGODB_PORT=27017
MONGODB_DATABASE=cinephoria_read
MONGODB_USERNAME=root
MONGODB_PASSWORD=root123
MONGODB_AUTH_DATABASE=admin
```

## 📖 Utilisation

### Exemple basique
```php
use App\Infrastructure\Database\Models\MongoDB\CinemaPublic;

// Récupérer tous les cinémas actifs
$cinemas = CinemaPublic::actif()->get();

// Rechercher par ville
$pariscinemas = CinemaPublic::actif()->byLocation('Paris')->get();

// Pagination
$paginated = CinemaPublic::actif()->paginate(10);
```

### Avec les QueryHandlers
```php
// GetCinemasListMongoQueryHandler
$mongoQuery = CinemaPublic::query()
    ->actif()
    ->byLocation($query->location);

$paginator = $mongoQuery
    ->orderBy('nom')
    ->paginate(
        perPage: $query->perPage,
        page: $query->page
    );
```

## 🎯 Avantages des modèles Eloquent MongoDB

### ✅ Par rapport aux requêtes directes
- **Type Safety** : Propriétés typées et méthodes métier
- **Scopes réutilisables** : Logic métier encapsulée
- **Eloquent Features** : Relations, mutators, accessors
- **Testing** : Facilité de test avec factories

### ✅ Performance optimisée
- **Collections spécialisées** : Données dénormalisées pour lecture
- **Indexes MongoDB** : Optimisation automatique des requêtes
- **Pagination native** : Pas de OFFSET/LIMIT complexes
- **JSON natif** : Pas de sérialisation/désérialisation

## 🔄 Synchronisation PostgreSQL → MongoDB

La synchronisation se fait via les Event Listeners Laravel :
```php
// Domain Events (PostgreSQL)
CinemaCreated::class → CinemaCreatedListener::class
CinemaUpdated::class → CinemaUpdatedListener::class

// Listeners mettent à jour MongoDB
class CinemaUpdatedListener {
    public function handle(CinemaUpdated $event): void {
        CinemaPublic::updateOrCreate(
            ['cinema_id' => $event->cinema->id->value],
            $this->mapToMongoData($event->cinema)
        );
    }
}
```

## 📊 Structure des données

### CinemaPublic
```json
{
  "_id": ObjectId("..."),
  "cinema_id": "uuid-string",
  "nom": "Cinéma Champs-Élysées",
  "adresse": "123 Avenue des Champs-Élysées",
  "ville": "Paris",
  "code_postal": "75008",
  "telephone": "0123456789",
  "email": "contact@cinema.fr",
  "statut": "actif",
  "services": "[\"PMR\", \"4K\", \"IMAX\"]",
  "nombre_salles": 8,
  "salles": "[{\"nom\": \"Salle 1\", \"capacite\": 200}]",
  "horaires_ouverture": "{\"lundi\": \"10:00-22:00\"}",
  "created_at": ISODate("..."),
  "updated_at": ISODate("...")
}
```

## 🧪 Tests

### Test des modèles
```php
// tests/Feature/Application/Cinema/Queries/GetCinemasListMongoQueryHandlerTest.php
public function testCanRetrieveCinemasFromMongoDB(): void
{
    CinemaPublic::create([/* données test */]);
    $result = $this->handler->handle($query);
    $this->assertTrue($result->isSuccess());
}
```

### Factory MongoDB (optionnel)
```php
// database/factories/MongoDB/CinemaPublicFactory.php
class CinemaPublicFactory extends Factory
{
    protected $model = CinemaPublic::class;

    public function definition(): array
    {
        return [
            'cinema_id' => $this->faker->uuid,
            'nom' => 'Cinéma ' . $this->faker->city,
            // ...
        ];
    }
}
```

## 🎯 Bonnes pratiques

### 1. Gestion des JSON strings
```php
// ❌ Mauvais : cast automatique peut échouer
protected $casts = ['services' => 'array'];

// ✅ Bon : gestion manuelle avec fallback
public function getServices(): array
{
    $services = $this->services;
    if (is_string($services)) {
        return json_decode($services, true) ?: [];
    }
    return is_array($services) ? $services : [];
}
```

### 2. Scopes réutilisables
```php
// ✅ Encapsuler la logique métier
public function scopeActif($query) {
    return $query->where('statut', 'actif');
}

public function scopeByLocation($query, ?string $location) {
    if (!$location) return $query;
    return $query->where('ville', 'like', "%{$location}%");
}
```

### 3. Méthodes métier
```php
// ✅ Logic accessible depuis les DTOs
public function hasAccessibilityPmr(): bool
{
    return in_array('PMR', $this->getServices()) ||
           $this->nombre_salles >= 6;
}
```

---

**Package utilisé** : `mongodb/laravel-mongodb` v5.5.0
**Laravel** : v12
**Architecture** : CQRS Read-Side optimisé