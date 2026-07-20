# 🎬 Cinéphoria

**Application web de gestion de cinéma** — Projet ECF (RNCP 37873 — Concepteur Développeur d'Applications)

<div align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/PostgreSQL-17-336791?style=for-the-badge&logo=postgresql&logoColor=white" />
  <img src="https://img.shields.io/badge/MongoDB-7.0-47A248?style=for-the-badge&logo=mongodb&logoColor=white" />
  <img src="https://img.shields.io/badge/Redis-7-DC382D?style=for-the-badge&logo=redis&logoColor=white" />
</div>

---

## 📋 Description

Cinéphoria est une application de gestion de cinéma pour une chaîne franco-belge de 7 établissements. Elle permet aux spectateurs de consulter le catalogue, de réserver des places et de suivre leurs réservations, et offre aux équipes un espace professionnel de suivi de l'activité et d'administration de la programmation.

Le projet repose sur une **architecture en couches** séparant la présentation, la logique métier et l'accès aux données.

### 🎯 Fonctionnalités

**Espace public**
- Catalogue de films avec filtres par genre et classification
- Consultation des cinémas (adresse, horaires, accessibilité)
- Fiche film détaillée : synopsis, séances du jour, planning à venir
- Réservation de places par catégorie tarifaire

**Espace client**
- Suivi de ses réservations
- Billets au format QR code

**Espace employé** — consultation opérationnelle
- Tableau de bord du jour (séances, réservations, films)
- Planning détaillé des séances avec taux de remplissage
- Suivi des réservations du jour
- Programmation du jour par film

**Espace administration**
- Gestion des films et des séances
- Gestion des salles et des cinémas

---

## 🏗️ Architecture des données

L'architecture repose sur une **séparation des flux de lecture et d'écriture**, afin d'optimiser à la fois l'intégrité des écritures et les performances de lecture.

### PostgreSQL — flux d'écriture

Source de vérité de l'application. Il gère les données transactionnelles (réservations, paiements, création des séances) où l'intégrité relationnelle et le respect des propriétés ACID sont critiques, via les transactions SQL.

### MongoDB — flux de lecture

Dédié au catalogue public (films et séances).

En SQL, afficher le planning d'un film avec les informations de la salle et du cinéma nécessiterait de multiples jointures coûteuses. MongoDB résout cela par la **dénormalisation** : les informations de jointure (`film_titre`, `cinema_nom`) sont dupliquées directement dans le document `seance`, ce qui permet une lecture en un seul appel.

Les séances sont isolées dans leur propre collection car elles ont une durée de vie limitée : un **index TTL** les supprime automatiquement après leur passage, ce qui évite la croissance infinie de la base et l'écriture de tâches planifiées de nettoyage.

### Redis et synchronisation

Redis assure la gestion des **sessions** et des **files d'attente**.

La synchronisation PostgreSQL → MongoDB distingue deux cas :

**Les réservations sont traitées de façon synchrone.** Lorsqu'une réservation est validée, le compteur de places disponibles est mis à jour immédiatement dans MongoDB, dans la continuité de la transaction. Le stock affiché reste ainsi fidèle à la réalité.

**Le reste du catalogue est synchronisé en arrière-plan**, via Laravel Horizon et des workers : ajout ou modification de films, création de séances, mise à jour des informations de programmation. Ces opérations n'ont pas d'exigence d'immédiateté et sont traitées sans impacter les performances de la base transactionnelle.

### Fiabilité des réservations concurrentes

La garantie finale repose sur PostgreSQL : le décrément des places s'effectue dans une requête conditionnelle unique (`WHERE places_disponibles >= n`).

Si deux clients tentent de réserver les mêmes dernières places au même instant, le premier passe, le second attend puis relit le nombre de places à jour. Il n'est refusé que s'il ne reste réellement plus assez de places — jamais par erreur.

Le résultat : des performances de lecture optimales pour les visiteurs qui consultent le catalogue, et une fiabilité absolue sur les transactions.

---

## 🚀 Installation locale

### Prérequis

- [Docker](https://docs.docker.com/get-docker/) et Docker Compose
- [Castor](https://castor.jolicode.com/) — outil de tâches PHP
- Git

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/kadevland/cinephoria-web-ecf-2025.git
cd cinephoria-web-ecf-2025

# 2. Configurer l'environnement
cp .env.example .env

# 3. Construire les images Docker
castor docker:dev:build

# 4. Démarrer les conteneurs (application, PostgreSQL, MongoDB, Redis)
castor docker:dev:up

# 5. Appliquer les migrations de base de données
castor artisan migrate

# 6. Charger le jeu de données de démonstration
castor artisan init:dump-data
```

L'application est alors accessible sur **http://localhost**.

### Commandes utiles

```bash
castor docker:dev:up          # Démarrer l'environnement
castor docker:dev:down        # Arrêter l'environnement
castor artisan <commande>     # Exécuter une commande Artisan
```

---

## 👤 Comptes de démonstration

| Profil | Identifiant | Mot de passe |
|---|---|---|
| Administrateur | `admin@cinephoria.fr` | `Admin123!@#` |
| Employé | `jean.dupont@cinephoria-lille.fr` | `Employe123!@#` |
| Client | `thomas.dubois@outlook.com` | `Client123!@#!` |

---

## 📦 Technologies

### Backend
- **Laravel 12** — framework applicatif
- **PHP 8.4**
- **Laravel Octane** — serveur applicatif haute performance
- **PostgreSQL 17** — données transactionnelles
- **MongoDB 7** — cache de lecture du catalogue
- **Redis 7** — sessions et files d'attente
- **Laravel Horizon** — supervision des traitements en arrière-plan
- **Laravel Sanctum** — authentification des accès API

### Frontend
- **Blade** — rendu côté serveur
- **Tailwind CSS v4** — styles
- **Preline UI** — bibliothèque de composants d'interface
- **Alpine.js** — interactivité légère, sans application monopage
- **HTMX** — mises à jour partielles de page sans rechargement
- **Leaflet** + **OpenStreetMap** — cartographie des cinémas
- **Vite** — compilation des assets

### Packages notables
- **MoneyPHP** — calculs monétaires précis, sans erreur d'arrondi sur les prix
- **endroid/qr-code** — génération des QR codes de billets
- **spatie/laravel-pdf** et **dompdf** — génération de documents PDF
- **respect/validation** — règles de validation métier
- **laravel-phone** et **postal-code-validation** — validation des formats téléphone et code postal
- **Castor** — automatisation des tâches de développement

### Qualité et outillage
- **Pest 4** — tests
- **Larastan 3** — analyse statique
- **Laravel Pint** — formatage du code
- **CaptainHook** — vérifications automatiques avant commit
- **Laravel Debugbar** et **Pail** — débogage et suivi des journaux

## 🧪 Tests

```bash
castor artisan test              # Lancer les tests
castor artisan test --parallel   # Exécution en parallèle
```

Les tests couvrent en priorité les règles métier sensibles : validation des formats, exactitude des calculs de prix, cohérence des réservations.

---

## 🔄 Intégration et déploiement continus

**Intégration continue** — à chaque *pull request*, un workflow GitHub Actions exécute automatiquement les tests (Pest) et l'analyse statique (Larastan). Aucune modification défaillante ne peut être fusionnée.

**Déploiement continu** — le déploiement est géré par Coolify, déclenché automatiquement par un *webhook* GitHub à chaque mise à jour de la branche de production.

### Organisation Git

- `main` — branche de production, toujours stable
- `develop` — branche d'intégration
- `feature/*` — une branche par fonctionnalité, issue de `develop`

---

## 🔒 Sécurité

| Risque | Mesure |
|---|---|
| Injection SQL | ORM Eloquent (requêtes préparées), validation via Form Requests |
| Failles XSS | Échappement automatique des vues Blade |
| CSRF | Jetons natifs Laravel sur tous les formulaires |
| Mots de passe | Hachage Argon2id, identifiants isolés dans une table dédiée |
| Force brute | Limitation du taux de requêtes |
| Exposition d'identifiants | Identifiants publics non séquentiels dans les URL |

---

## 📄 Licence

Projet éducatif réalisé dans le cadre de la certification RNCP 37873.

---

<div align="center">
  <p>Développé pour la certification Concepteur Développeur d'Applications</p>
</div>
