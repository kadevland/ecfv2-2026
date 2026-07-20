# Architecture Bus CQRS - Admin vs Public

Cette documentation explique la séparation entre les bus admin et public dans notre architecture CQRS.

## 🏗️ Vue d'ensemble

### Séparation Admin / Public
- **Admin** : PostgreSQL + Entités Domain + QueryBus/CommandBus
- **Public** : MongoDB + Modèles Eloquent + PublicQueryBus

## 📋 Bus disponibles

### 1. CommandBus (Admin uniquement)
- **Usage** : Commandes d'écriture (CREATE, UPDATE, DELETE)
- **Database** : PostgreSQL
- **Entités** : Domain entities avec logique métier
- **Handlers** : Utilisent repositories et agrégats

```php
// Exemple : Créer un cinéma (Admin)
use App\Application\Bus\CommandBus;
use App\Application\Cinema\Commands\CreateCinema\CreateCinemaCommand;

$commandBus = app(CommandBus::class);
$command = new CreateCinemaCommand(/* ... */);
$result = $commandBus->dispatch($command);
```

### 2. QueryBus (Admin uniquement)
- **Usage** : Requêtes admin complexes avec relations
- **Database** : PostgreSQL
- **Entités** : Domain entities chargées avec relations
- **Performance** : Plus lent mais données complètes

```php
// Exemple : Liste admin avec relations (PostgreSQL)
use App\Application\Bus\QueryBus;
use App\Application\Cinema\Queries\GetCinemasList\GetCinemasListQuery;

$queryBus = app(QueryBus::class);
$query = new GetCinemasListQuery(page: 1, perPage: 20);
$result = $queryBus->ask($query);
```

### 3. PublicQueryBus (Public uniquement)
- **Usage** : Requêtes publiques optimisées pour lecture
- **Database** : MongoDB
- **Modèles** : Eloquent MongoDB avec données dénormalisées
- **Performance** : Très rapide, données pré-agrégées

```php
// Exemple : Liste publique optimisée (MongoDB)
use App\Application\Bus\PublicQueryBus;
use App\Application\Public\Cinema\Queries\GetPublicCinemasList\GetPublicCinemasListQuery;

$publicQueryBus = app(PublicQueryBus::class);
$query = new GetPublicCinemasListQuery(page: 1, perPage: 12);
$result = $publicQueryBus->ask($query);
```

## 🗂️ Structure des dossiers

```
app/Application/
├── Bus/
│   ├── CommandBus.php          # Admin - Commands PostgreSQL
│   ├── QueryBus.php            # Admin - Queries PostgreSQL
│   └── PublicQueryBus.php      # Public - Queries MongoDB
├── Cinema/                     # Admin - Domain
│   ├── Commands/               # CREATE/UPDATE/DELETE
│   └── Queries/                # Admin queries PostgreSQL
└── Public/                     # Public - Optimized
    ├── Cinema/
    │   └── Queries/            # Public queries MongoDB
    ├── Film/
    └── Reservation/
```

## 🎯 Mapping Controllers → Bus

### Controllers Admin
```php
// app/Http/Controllers/Admin/Cinema/ListCinemasController.php
class ListCinemasController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus // PostgreSQL
    ) {}

    public function __invoke(Request $request): View
    {
        $query = GetCinemasListRequestMapper::toQuery($request);
        $result = $this->queryBus->ask($query); // PostgreSQL

        return view('admin.cinemas.index', [
            'cinemas' => $result->getValue()->cinemas
        ]);
    }
}
```

### Controllers Public
```php
// app/Http/Controllers/Public/ListCinemasController.php
class ListCinemasController extends Controller
{
    public function __construct(
        private readonly PublicQueryBus $publicQueryBus // MongoDB
    ) {}

    public function __invoke(Request $request): View
    {
        $query = GetPublicCinemasListRequestMapper::toQuery($request);
        $result = $this->publicQueryBus->ask($query); // MongoDB

        return view('public.cinemas.index', [
            'cinemas' => $result->getValue()->cinemas
        ]);
    }
}
```

## 🔄 Synchronisation PostgreSQL → MongoDB

### Event Listeners
```php
// Domain Events déclenchés par CommandBus
CinemaCreated::class → CinemaCreatedListener::class
CinemaUpdated::class → CinemaUpdatedListener::class
CinemaDeleted::class → CinemaDeletedListener::class

// Listeners synchronisent vers MongoDB
class CinemaUpdatedListener
{
    public function handle(CinemaUpdated $event): void
    {
        CinemaPublic::updateOrCreate(
            ['cinema_id' => $event->cinema->id->value],
            $this->mapToMongoData($event->cinema)
        );
    }
}
```

## 📊 Comparaison Performance

### Admin (PostgreSQL + QueryBus)
```sql
-- Requête complexe avec JOINs
SELECT c.*, COUNT(s.id) as salles_count
FROM cinemas c
LEFT JOIN salles s ON s.cinema_id = c.id
WHERE c.statut = 'actif'
GROUP BY c.id
ORDER BY c.nom
LIMIT 20 OFFSET 0;
```

### Public (MongoDB + PublicQueryBus)
```javascript
// Requête simple sur collection dénormalisée
db.cinema_publics.find(
  { statut: "actif" }
).sort({ nom: 1 })
 .skip(0)
 .limit(12);
```

## ⚙️ Configuration

### Bus Registration (BusServiceProvider)
```php
// Bus Admin (PostgreSQL)
$this->app->singleton(QueryBus::class, function (Container $app) {
    return new QueryBus(
        $app->make(QueryRegistry::class),
        $app->make(HandlerProviderInterface::class)
    );
});

// Bus Public (MongoDB)
$this->app->singleton(PublicQueryBus::class, function (Container $app) {
    return new PublicQueryBus();
});
```

### Handler Mappings
```php
// config/bus.php - Admin handlers
'query_mappings' => [
    GetCinemasListQuery::class => GetCinemasListQueryHandler::class, // PostgreSQL
],

// PublicQueryBus - Public handlers (dans le bus directement)
private array $publicQueryMappings = [
    GetPublicCinemasListQuery::class => GetPublicCinemasListQueryHandler::class, // MongoDB
];
```

## 🧪 Tests

### Test Admin Bus
```php
public function testAdminCinemasList(): void
{
    $queryBus = app(QueryBus::class);
    $query = new GetCinemasListQuery(page: 1, perPage: 10);
    $result = $queryBus->ask($query);

    $this->assertTrue($result->isSuccess());
    // Vérifie données complètes avec relations
}
```

### Test Public Bus
```php
public function testPublicCinemasList(): void
{
    $publicQueryBus = app(PublicQueryBus::class);
    $query = new GetPublicCinemasListQuery(page: 1, perPage: 12);
    $result = $publicQueryBus->ask($query);

    $this->assertTrue($result->isSuccess());
    // Vérifie données optimisées pour affichage
}
```

## 🎯 Bonnes pratiques

### ✅ Admin
- Utiliser QueryBus/CommandBus pour les opérations admin
- Charger les relations nécessaires via Eloquent
- Priorité à la cohérence des données
- Performances secondaires

### ✅ Public
- Utiliser PublicQueryBus pour les affichages publics
- Données dénormalisées pour éviter les JOINs
- Priorité aux performances
- Fallbacks pour les erreurs

### ❌ À éviter
- Mélanger les bus (admin avec PublicQueryBus)
- Utiliser MongoDB pour les writes
- Ignorer la synchronisation PostgreSQL → MongoDB
- Exposer les données admin dans les vues publiques

---

**Architecture** : CQRS avec séparation claire Admin/Public
**Performance** : Optimisée pour chaque contexte d'usage
**Maintenabilité** : Bus séparés avec responsabilités distinctes