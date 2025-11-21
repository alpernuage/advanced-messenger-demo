# Outils de Qualité de Code et CI/CD

## 🎯 Philosophie

Le code doit être **propre, maintenable et sans erreurs** avant même d'être commité. Les outils CI/CD garantissent cette qualité automatiquement.

---

## 🛠️ Outils de Qualité Recommandés

### 1. PHP-CS-Fixer

**Rôle** : Formatage automatique du code selon PSR-12

**Installation** :
```bash
composer require --dev friendsofphp/php-cs-fixer
```

**Configuration** (`.php-cs-fixer.dist.php`) :
```php
<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true);
```

**Commandes** :
```bash
# Vérifier
vendor/bin/php-cs-fixer fix --dry-run --diff

# Corriger
vendor/bin/php-cs-fixer fix
```

---

### 2. PHPStan

**Rôle** : Analyse statique pour détecter les erreurs de type et bugs potentiels

**Installation** :
```bash
composer require --dev phpstan/phpstan
composer require --dev phpstan/extension-installer
composer require --dev phpstan/phpstan-symfony
composer require --dev phpstan/phpstan-doctrine
```

**Configuration** (`phpstan.neon`) :
```neon
parameters:
    level: max
    paths:
        - src
        - tests
    excludePaths:
        - tests/bootstrap.php
    symfony:
        container_xml_path: var/cache/dev/App_KernelDevDebugContainer.xml
    doctrine:
        objectManagerLoader: tests/object-manager.php
    checkMissingIterableValueType: true
    checkGenericClassInNonGenericObjectType: true
```

**Commandes** :
```bash
vendor/bin/phpstan analyse
```

---

### 3. Rector

**Rôle** : Refactoring automatique et modernisation du code

**Installation** :
```bash
composer require --dev rector/rector
```

**Configuration** (`rector.php`) :
```php
<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSets([
        LevelSetList::UP_TO_PHP_83,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        SymfonySetList::SYMFONY_70,
        SymfonySetList::SYMFONY_CODE_QUALITY,
    ])
    ->withTypeCoverageLevel(100);
```

**Commandes** :
```bash
# Vérifier
vendor/bin/rector process --dry-run

# Appliquer
vendor/bin/rector process
```

---

### 4. PHPMD (PHP Mess Detector)

**Rôle** : Détection de code smell et mauvaises pratiques

**Installation** :
```bash
composer require --dev phpmd/phpmd
```

**Configuration** (`phpmd.xml`) :
```xml
<?xml version="1.0"?>
<ruleset name="Custom PHPMD Rules">
    <description>Custom PHPMD ruleset</description>

    <rule ref="rulesets/cleancode.xml">
        <exclude name="StaticAccess"/>
    </rule>
    <rule ref="rulesets/codesize.xml"/>
    <rule ref="rulesets/controversial.xml"/>
    <rule ref="rulesets/design.xml"/>
    <rule ref="rulesets/naming.xml">
        <exclude name="ShortVariable"/>
    </rule>
    <rule ref="rulesets/unusedcode.xml"/>
</ruleset>
```

**Commandes** :
```bash
vendor/bin/phpmd src,tests text phpmd.xml
```

---

### 5. PHPUnit (avec Coverage)

**Rôle** : Tests unitaires et fonctionnels avec couverture de code

**Configuration** (`phpunit.xml.dist`) :
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         failOnRisky="true"
         failOnWarning="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Functional">
            <directory>tests/Functional</directory>
        </testsuite>
    </testsuites>

    <coverage>
        <report>
            <html outputDirectory="var/coverage"/>
            <text outputFile="php://stdout" showUncoveredFiles="true"/>
        </report>
    </coverage>

    <source>
        <include>
            <directory>src</directory>
        </include>
    </source>
</phpunit>
```

**Commandes** :
```bash
vendor/bin/phpunit
vendor/bin/phpunit --coverage-html var/coverage
```

---

## 🚀 Configuration CI/CD

### GitHub Actions (`.github/workflows/ci.yml`)

```yaml
name: CI

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  quality:
    name: Code Quality
    runs-on: ubuntu-latest

    services:
      postgres:
        image: postgres:16-alpine
        env:
          POSTGRES_DB: messenger_demo_test
          POSTGRES_USER: messenger
          POSTGRES_PASSWORD: secret
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5

      redis:
        image: redis:7-alpine
        options: >-
          --health-cmd "redis-cli ping"
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5

    steps:
      - name: Checkout
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: pdo_pgsql, redis
          coverage: xdebug
          tools: composer:v2

      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress

      - name: Check Code Style (PHP-CS-Fixer)
        run: vendor/bin/php-cs-fixer fix --dry-run --diff --verbose

      - name: Static Analysis (PHPStan)
        run: vendor/bin/phpstan analyse --error-format=github

      - name: Code Quality (PHPMD)
        run: vendor/bin/phpmd src,tests github phpmd.xml

      - name: Refactoring Check (Rector)
        run: vendor/bin/rector process --dry-run

      - name: Run Tests
        run: vendor/bin/phpunit --coverage-text
        env:
          DATABASE_URL: postgresql://messenger:secret@postgres:5432/messenger_demo_test
          MESSENGER_TRANSPORT_DSN: redis://redis:6379/messages

      - name: Upload Coverage
        uses: codecov/codecov-action@v3
        with:
          files: ./var/coverage/clover.xml
```

---

### GitLab CI (`.gitlab-ci.yml`)

```yaml
image: php:8.3-cli

variables:
  POSTGRES_DB: messenger_demo_test
  POSTGRES_USER: messenger
  POSTGRES_PASSWORD: secret
  DATABASE_URL: "postgresql://messenger:secret@postgres:5432/messenger_demo_test"
  MESSENGER_TRANSPORT_DSN: "redis://redis:6379/messages"

services:
  - postgres:16-alpine
  - redis:7-alpine

stages:
  - quality
  - test

before_script:
  - apt-get update -yqq
  - apt-get install -yqq git libpq-dev
  - docker-php-ext-install pdo_pgsql
  - pecl install redis && docker-php-ext-enable redis
  - curl -sS https://getcomposer.org/installer | php
  - php composer.phar install --prefer-dist --no-progress

code-style:
  stage: quality
  script:
    - vendor/bin/php-cs-fixer fix --dry-run --diff --verbose

static-analysis:
  stage: quality
  script:
    - vendor/bin/phpstan analyse

code-quality:
  stage: quality
  script:
    - vendor/bin/phpmd src,tests text phpmd.xml

refactoring:
  stage: quality
  script:
    - vendor/bin/rector process --dry-run

tests:
  stage: test
  script:
    - vendor/bin/phpunit --coverage-text --colors=never
  coverage: '/^\s*Lines:\s*\d+.\d+\%/'
```

---

## 📝 Makefile - Commandes de Qualité

Ajouter au Makefile :

```makefile
.PHONY: quality cs-fix stan rector phpmd test-coverage

quality: cs-fix stan rector phpmd test ## Exécute tous les checks de qualité

cs-fix: ## Vérifie et corrige le formatage du code
docker-compose exec php vendor/bin/php-cs-fixer fix

cs-check: ## Vérifie le formatage sans corriger
docker-compose exec php vendor/bin/php-cs-fixer fix --dry-run --diff

stan: ## Analyse statique PHPStan
docker-compose exec php vendor/bin/phpstan analyse

rector: ## Vérifie les refactorings possibles
docker-compose exec php vendor/bin/rector process --dry-run

rector-fix: ## Applique les refactorings
docker-compose exec php vendor/bin/rector process

phpmd: ## Détection de code smell
docker-compose exec php vendor/bin/phpmd src,tests text phpmd.xml

test-coverage: ## Tests avec couverture de code
docker-compose exec php vendor/bin/phpunit --coverage-html var/coverage
@echo "Coverage report: var/coverage/index.html"

ci: cs-check stan phpmd test ## Simule le CI en local
```

---

## 🎯 Workflow de Développement Recommandé

### Avant chaque commit

```bash
make quality
```

Cette commande exécute :
1. ✅ Formatage du code (PHP-CS-Fixer)
2. ✅ Analyse statique (PHPStan)
3. ✅ Refactoring check (Rector)
4. ✅ Code smell detection (PHPMD)
5. ✅ Tests

### Avant chaque push

```bash
make ci
```

Simule exactement ce que le CI va exécuter.

---

## 📊 Badges pour README

Ajouter dans le README.md :

```markdown
![CI](https://github.com/username/AdvancedMessengerDemo/workflows/CI/badge.svg)
![PHPStan Level](https://img.shields.io/badge/PHPStan-level%20max-brightgreen.svg)
![PHP Version](https://img.shields.io/badge/PHP-8.3-blue.svg)
![Code Coverage](https://codecov.io/gh/username/AdvancedMessengerDemo/branch/main/graph/badge.svg)
```

---

## ✅ Résumé

### Outils Essentiels

| Outil | Rôle | Niveau |
|-------|------|--------|
| **PHP-CS-Fixer** | Formatage code | PSR-12 + Symfony |
| **PHPStan** | Analyse statique | Level max |
| **Rector** | Refactoring auto | PHP 8.3 + Symfony 7.0 |
| **PHPMD** | Code smell | Toutes règles |
| **PHPUnit** | Tests | Avec coverage |

### Commandes Rapides

```bash
make quality      # Tout vérifier et corriger
make ci          # Simuler le CI
make test-coverage # Tests avec rapport
```

### Résultat

- ✅ Code propre et formaté automatiquement
- ✅ Aucune erreur de type (PHPStan max)
- ✅ Code moderne (Rector PHP 8.3)
- ✅ Pas de code smell (PHPMD)
- ✅ Tests passants avec bonne couverture
- ✅ CI/CD qui valide automatiquement
