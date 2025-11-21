# Guide de Démarrage - Advanced Messenger Demo

## Prérequis

- Docker et Docker Compose installés
- Make (optionnel mais recommandé)

## Installation Rapide

### Option 1: Avec Make (Recommandé)

```bash
cd /advanced-messenger-demo
make setup
```

Cette commande va:
1. Construire les images Docker
2. Démarrer les conteneurs
3. Installer les dépendances Composer
4. Créer la base de données

### Option 2: Manuelle

```bash
cd /advanced-messenger-demo

# Construire et démarrer les conteneurs
docker-compose build
docker-compose up -d

# Installer les dépendances
docker-compose exec php composer install

# Créer la base de données
docker-compose exec php bin/console doctrine:database:create --if-not-exists
```

## Utilisation

### 1. Dispatcher une Commande

```bash
make dispatch
# ou
docker-compose exec php bin/console app:dispatch-order
```

Cette commande va:
- Créer un `PlaceOrderMessage` avec un ID et montant aléatoires
- Le dispatcher sur le bus (traitement synchrone)
- Logger l'opération

### 2. Consommer les Messages Asynchrones

Dans un terminal séparé:

```bash
make consume-async
# ou
docker-compose exec php bin/console messenger:consume async -vv
```

Vous verrez:
- Le `ProcessPaymentMessage` traité après 5 secondes
- Les tentatives de retry en cas d'échec (20% de chance)
- Le `SendConfirmationEmailMessage` si le paiement réussit

### 3. Consulter la Dead Letter Queue

Si des messages échouent définitivement (après 3 retries):

```bash
make consume-failed
# ou
docker-compose exec php bin/console messenger:consume failed -vv
```

## Scénarios de Test

### Scénario 1: Succès Complet

1. `make dispatch` - Dispatche une commande
2. `make consume-async` - Consomme les messages
3. Observer les logs:
   - ⏳ Début du traitement
   - 📦 Commande placée
   - ⏰ Paiement programmé avec délai de 5s
   - ✅ Message traité
   - (attente 5 secondes)
   - 💳 Tentative de paiement
   - ✅ Paiement réussi
   - 📧 Envoi de l'email de confirmation
   - ✅ Email envoyé

### Scénario 2: Échec avec Retry

1. `make dispatch`
2. `make consume-async`
3. Observer les retries automatiques (20% de chance d'échec)
   - ⚠️ Échec temporaire - retry programmé
   - Retry après 1s, 2s, puis 4s

### Scénario 3: Échec Permanent (DLQ)

1. Dispatcher plusieurs commandes jusqu'à obtenir un échec permanent
2. Observer le message dans la DLQ:
   - ❌ Échec permanent - envoi vers DLQ
3. `make consume-failed` pour traiter manuellement

## Tests

### Exécuter les Tests

```bash
make test
# ou
docker-compose exec php vendor/bin/phpunit
```

### Tests Disponibles

- **Unit/MessageHandler/PlaceOrderHandlerTest**: Vérifie le DelayStamp de 5000ms
- **Functional/Command/DispatchOrderCommandTest**: Vérifie le dispatch du message

## Commandes Utiles

```bash
make help              # Afficher toutes les commandes disponibles
make up                # Démarrer les conteneurs
make down              # Arrêter les conteneurs
make logs              # Afficher les logs
make shell             # Ouvrir un shell dans le conteneur PHP
```

## Dépannage

### Les conteneurs ne démarrent pas

```bash
make down
make build
make up
```

### Erreur de connexion à la base de données

Vérifier que PostgreSQL est bien démarré:

```bash
docker-compose ps
docker-compose logs postgres
```

### Les messages ne sont pas consommés

Vérifier que Redis est accessible:

```bash
docker-compose exec php php -r "var_dump(extension_loaded('redis'));"
```

## Structure du Projet

```
AdvancedMessengerDemo/
├── .ai_documents/                          # Documentation
│   ├── project_specifications.md
│   ├── setup_guide.md
│   └── messenger_project_prompt_final.md
├── config/
│   └── packages/
│       ├── messenger.yaml        # Configuration Messenger
│       ├── framework.yaml
│       └── doctrine.yaml
├── src/
│   ├── Command/
│   │   └── DispatchOrderCommand.php
│   ├── Message/
│   │   ├── PlaceOrderMessage.php
│   │   ├── ProcessPaymentMessage.php
│   │   └── SendConfirmationEmailMessage.php
│   ├── MessageHandler/
│   │   ├── PlaceOrderHandler.php
│   │   ├── ProcessPaymentHandler.php
│   │   └── SendConfirmationEmailHandler.php
│   ├── Middleware/
│   │   └── TimingMiddleware.php
│   └── Kernel.php
├── tests/
│   ├── Unit/
│   └── Functional/
├── docker-compose.yaml
├── Dockerfile
├── Makefile
└── composer.json
```
