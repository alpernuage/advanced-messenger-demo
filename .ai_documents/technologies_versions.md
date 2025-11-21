# Technologies et Versions Utilisées

## 📦 Stack Technique Complète

### Backend

| Technologie | Version | Rôle |
|------------|---------|------|
| **PHP** | 8.3 | Langage de programmation |
| **Symfony** | 7.0 | Framework PHP |
| **Composer** | 2.x | Gestionnaire de dépendances |

### Base de Données

| Technologie | Version | Rôle |
|------------|---------|------|
| **PostgreSQL** | 16 | Base de données relationnelle |
| **Doctrine ORM** | 3.0 | ORM pour PHP |
| **Doctrine Bundle** | 2.11 | Intégration Doctrine/Symfony |

### Message Queue

| Technologie | Version | Rôle |
|------------|---------|------|
| **Redis** | 7 | Transport asynchrone |
| **Symfony Messenger** | 7.0 | Composant de messaging |
| **Doctrine Messenger** | 7.0 | Transport Doctrine |
| **Redis Messenger** | 7.0 | Transport Redis |

### Infrastructure

| Technologie | Version | Rôle |
|------------|---------|------|
| **Docker** | Latest | Conteneurisation |
| **Docker Compose** | 3.8 | Orchestration de conteneurs |
| **Alpine Linux** | Latest | OS de base pour conteneurs |

### Testing

| Technologie | Version | Rôle |
|------------|---------|------|
| **PHPUnit** | 10.0 | Framework de tests |
| **Symfony PHPUnit Bridge** | 7.0 | Intégration PHPUnit/Symfony |

### Outils de Développement

| Technologie | Version | Rôle |
|------------|---------|------|
| **Make** | - | Automatisation des commandes |
| **Git** | - | Contrôle de version |

---

## 📋 Dépendances Composer Détaillées

### Production (require)

```json
{
    "php": ">=8.3",
    "symfony/console": "^7.0",
    "symfony/framework-bundle": "^7.0",
    "symfony/messenger": "^7.0",
    "symfony/doctrine-messenger": "^7.0",
    "symfony/redis-messenger": "^7.0",
    "symfony/dotenv": "^7.0",
    "symfony/flex": "^2.4",
    "symfony/runtime": "^7.0",
    "symfony/yaml": "^7.0",
    "doctrine/doctrine-bundle": "^2.11",
    "doctrine/orm": "^3.0"
}
```

### Développement (require-dev)

```json
{
    "phpunit/phpunit": "^10.0",
    "symfony/phpunit-bridge": "^7.0"
}
```

---

## 🐳 Images Docker Utilisées

### PHP Container

```dockerfile
FROM php:8.3-fpm-alpine
```

**Extensions PHP installées** :
- `pdo_pgsql` : Connexion PostgreSQL
- `redis` : Connexion Redis

### PostgreSQL Container

```yaml
image: postgres:16-alpine
```

### Redis Container

```yaml
image: redis:7-alpine
```

---

## �� Versions des Composants Symfony

| Composant | Version | Description |
|-----------|---------|-------------|
| `symfony/console` | 7.0 | Commandes CLI |
| `symfony/framework-bundle` | 7.0 | Bundle principal |
| `symfony/messenger` | 7.0 | Système de messaging |
| `symfony/doctrine-messenger` | 7.0 | Transport Doctrine |
| `symfony/redis-messenger` | 7.0 | Transport Redis |
| `symfony/dotenv` | 7.0 | Gestion des variables d'env |
| `symfony/flex` | 2.4 | Gestionnaire de recettes |
| `symfony/runtime` | 7.0 | Runtime Symfony |
| `symfony/yaml` | 7.0 | Parser YAML |
| `symfony/phpunit-bridge` | 7.0 | Bridge PHPUnit |

---

## 📊 Compatibilité

### Versions Minimales Requises

- **PHP** : 8.3 ou supérieur
- **PostgreSQL** : 16 ou supérieur
- **Redis** : 7 ou supérieur
- **Docker** : 20.10 ou supérieur
- **Docker Compose** : 2.0 ou supérieur

### Extensions PHP Requises

- `pdo_pgsql` : Pour PostgreSQL
- `redis` : Pour Redis
- `json` : Pour JSON (inclus par défaut)
- `mbstring` : Pour les chaînes multi-octets (inclus par défaut)

---

## 🔄 Mise à Jour des Versions

Pour mettre à jour les dépendances :

```bash
# Mettre à jour Composer
docker-compose exec php composer update

# Mettre à jour les images Docker
docker-compose pull
docker-compose build --no-cache
```

---

## 📝 Notes sur les Versions

### Pourquoi PHP 8.3 ?

- Support des **readonly classes** (requis pour les messages)
- **Typage strict** amélioré
- **Performances** optimisées
- Support des **attributs PHP** (requis pour `#[AsMessageHandler]`)

### Pourquoi Symfony 7.0 ?

- Dernière version stable
- Support complet de **PHP 8.3**
- Améliorations de **Messenger** (retry strategy, DLQ)
- **MicroKernelTrait** simplifié

### Pourquoi PostgreSQL 16 ?

- Dernière version stable
- **Performances** améliorées
- Support des **JSON** avancé
- Fiabilité pour le **transport Doctrine**

### Pourquoi Redis 7 ?

- Dernière version stable
- **Performances** optimales pour le messaging
- Support des **streams** (utilisé par Messenger)
- Faible latence

---

## 🎯 Résumé des Versions Clés

```
PHP:        8.3
Symfony:    7.0
PostgreSQL: 16
Redis:      7
PHPUnit:    10.0
Doctrine:   3.0
```

Ces versions garantissent :
- ✅ Compatibilité totale
- ✅ Fonctionnalités modernes
- ✅ Performances optimales
- ✅ Support à long terme
