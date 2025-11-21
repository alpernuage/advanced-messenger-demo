.PHONY: help up down build composer setup-clean setup db-create db-migrate db-setup consume-async consume-failed dispatch test clean restart

help: ## 📖 Affiche cette aide
	@echo ""
	@echo "╔════════════════════════════════════════════════════════════╗"
	@echo "║         🚀 Advanced Messenger Demo - Commandes            ║"
	@echo "╚════════════════════════════════════════════════════════════╝"
	@echo ""
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk '\
	BEGIN {FS = ":.*?## "}; \
	/^##/ {gsub(/^## /, "", $$0); printf "\n\033[1;33m%s\033[0m\n", $$0; next}; \
	/^[^#]/ {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'
	@echo ""


## 🐳 DOCKER

build: ## 🐳 Construit les images Docker
	docker-compose build

up: ## 🐳 Démarre les conteneurs Docker
	docker-compose up -d

down: ## 🐳 Arrête les conteneurs Docker
	docker-compose down

logs: ## 🐳 Affiche les logs des conteneurs
	docker-compose logs -f

## 📦 INSTALLATION

composer: ## 📦 Installe les dépendances Composer
	docker-compose exec php composer install

setup-clean: ## 🔧 Réinstalle proprement tout le projet
	docker-compose down
	docker-compose build --no-cache
	docker-compose up -d

setup: build up composer db-setup ## 📦 Installation complète du projet
	@echo ""
	@echo "╔════════════════════════════════════════════════════════════╗"
	@echo "║              ✅ Projet installé avec succès!              ║"
	@echo "╚════════════════════════════════════════════════════════════╝"
	@echo ""
	@echo "📝 Prochaines étapes:"
	@echo "  1️⃣  make dispatch          → Dispatcher une commande"
	@echo "  2️⃣  make consume-async     → Consommer les messages (nouveau terminal)"
	@echo "  3️⃣  make test              → Exécuter les tests"
	@echo ""

## 🗄️  BASE DE DONNÉES

db-create: ## 🗄️  Crée la base de données
	docker-compose exec php bin/console doctrine:database:create --if-not-exists

db-migrate: ## 🗄️  Exécute les migrations
	docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction

db-setup: db-create ## 🗄️  Configuration complète de la BDD (création uniquement, pas de fixtures nécessaires)
	@echo "✅ Base de données créée (les tables Messenger seront créées automatiquement)"

## 🔍 QUALITÉ DE CODE

cs-fixer: ## 🔍 Corrige le style du code (PHP-CS-Fixer)
	docker-compose exec php vendor/bin/php-cs-fixer fix

phpstan: ## 🔍 Analyse statique du code (PHPStan)
	docker-compose exec php vendor/bin/phpstan analyse

rector: ## 🔍 Refactoring automatique (Rector)
	docker-compose exec php vendor/bin/rector process

ci: cs-fixer phpstan test ## 🔍 Lance toute la CI (CS-Fixer, PHPStan, Tests)
	@echo "✅ CI terminée avec succès !"

## 📨 MESSENGER

dispatch: ## 📨 Dispatche une commande de test
	docker-compose exec php bin/console app:dispatch-order

consume-async: ## 📨 Consomme les messages du transport async
	docker-compose exec php bin/console messenger:consume async -vv

consume-failed: ## 📨 Consomme les messages du transport failed (DLQ)
	docker-compose exec php bin/console messenger:consume failed -vv

messenger-status: ## 📨 Affiche le statut des transports
	docker-compose exec php bin/console messenger:stats

## 🧪 TESTS

test: ## 🧪 Exécute tous les tests PHPUnit
	docker-compose exec php vendor/bin/phpunit

test-unit: ## 🧪 Exécute uniquement les tests unitaires
	docker-compose exec php vendor/bin/phpunit --testsuite=Unit

test-functional: ## 🧪 Exécute uniquement les tests fonctionnels
	docker-compose exec php vendor/bin/phpunit --testsuite=Functional

test-coverage: ## 🧪 Génère le rapport de couverture de code
	docker-compose exec php vendor/bin/phpunit --coverage-html var/coverage

## 🛠️  DÉVELOPPEMENT

shell: ## 🛠️  Ouvre un shell dans le conteneur PHP
	docker-compose exec php sh

restart: down up ## 🛠️  Redémarre tous les conteneurs
