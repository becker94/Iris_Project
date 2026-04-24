# Tests — Heritage Motors

## Stack

- PHPUnit 11 via Symfony PHPUnit Bridge
- WebTestCase (tests fonctionnels HTTP)
- MockHttpClient (zéro dépendance vers Directus)
- SQLite (zéro dépendance vers MySQL pour les tests d'auth)

---

## Lancer les tests

```bash
php bin/phpunit
```

Si l'ancienne config `phpunit.dist.xml` est référencée :

```bash
php bin/phpunit -c phpunit.dist.xml
```

---

## Prérequis

### 1. Extension SQLite PHP

Tests d'authentification (checkout + logout) utilisent SQLite :

```bash
# Vérifier
php -m | grep pdo_sqlite

# Ubuntu/Debian
sudo apt install php-sqlite3

# Windows (WAMP/Laragon) : activer extension=pdo_sqlite dans php.ini
```

### 2. Aucun serveur externe requis

| Dépendance | Situation |
|-----------|-----------|
| Directus | **Mocké** — jamais contacté |
| Stripe | **Non utilisé** dans les tests |
| MySQL | **Remplacé** par SQLite (`var/test.db`) |

---

## Structure des tests

```
tests/
├── bootstrap.php                         # Chargement .env
├── Support/
│   └── DatabaseTestTrait.php             # Création schéma SQLite + utilisateur test
└── Controller/
    ├── VoitureControllerTest.php          # 3 tests — catalogue & fiche
    ├── CheckoutControllerTest.php         # 2 tests — accès protégé
    └── SecurityControllerTest.php         # 3 tests — login / logout
```

---

## Détail des tests

### VoitureControllerTest

| Test | Route | Dépendances | Doit passer |
|------|-------|-------------|-------------|
| `test_catalogue_page_returns_200` | GET /voitures | MockHttpClient | ✅ toujours |
| `test_catalogue_page_sans_directus` | GET /voitures | MockHttpClient (TransportException) | ✅ toujours |
| `test_fiche_voiture_returns_200_or_redirect` | GET /voiture/1 | MockHttpClient | ✅ toujours |

> Le contrôleur **ne doit jamais retourner 500** quand Directus est hors-ligne.
> Le bloc `#directus-error` s'affiche à la place du catalogue.

### CheckoutControllerTest

| Test | Route | Auth | DB | Doit passer |
|------|-------|------|----|-------------|
| `test_checkout_sans_connexion_redirige_login` | GET /checkout/1 | ❌ anonyme | ❌ | ✅ toujours |
| `test_checkout_avec_connexion` | GET /checkout/1 | ✅ ROLE_USER | ✅ SQLite | ✅ si SQLite dispo |

### SecurityControllerTest

| Test | Route | Auth | DB | Doit passer |
|------|-------|------|----|-------------|
| `test_login_page_returns_200` | GET /login | ❌ | ❌ | ✅ toujours |
| `test_login_post_mauvais_credentials` | POST /login | ❌ | ❌ | ✅ toujours |
| `test_logout_redirige` | GET /logout | ✅ | ✅ SQLite | ✅ si SQLite dispo |

---

## Modifications de production effectuées

### `src/Controller/VoitureController.php`

Ajout try/catch `TransportExceptionInterface` dans `index()` et `show()` :
- `index()` → rend le catalogue vide + variable `error` si Directus hors-ligne
- `show()` → redirige vers `/voitures` si Directus hors-ligne

### `templates/catalogue/index.html.twig`

Ajout bloc `#directus-error` affiché si la variable `error` est définie.

---

## Variables d'environnement test (`.env.test`)

```dotenv
KERNEL_CLASS='App\Kernel'
APP_SECRET='$ecretf0rt3st'
DIRECTUS_URL=http://localhost:8055   # valeur fictive, toujours mocké
DATABASE_URL="sqlite:///%kernel.project_dir%/var/test.db"
```

---

## Nettoyage

Le fichier `var/test.db` est recréé automatiquement à chaque test qui utilise `DatabaseTestTrait`.
Il peut être supprimé manuellement sans risque :

```bash
rm var/test.db
```
