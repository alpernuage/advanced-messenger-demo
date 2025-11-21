# 🚀 Advanced Symfony Messenger Demo

Projet de démonstration des fonctionnalités avancées de Symfony Messenger.

## ⚡ Démarrage Ultra-Rapide

```bash
cd /advanced-messenger-demo

# Installation complète (1 commande)
make setup

# Réinstallation propre du projet (tout reconstruire)
make setup-clean

# Dispatcher une commande
make dispatch

# Consommer les messages (nouveau terminal)
make consume-async
```

## 📋 Commandes Disponibles

Tapez `make help` pour voir toutes les commandes organisées par catégorie:

- 🐳 **Docker**: build, up, down, logs
- 📦 **Installation**: install, setup
- 🗄️ **Base de données**: db-create, db-setup
- 📨 **Messenger**: dispatch, consume-async, consume-failed, messenger-status
- 🧪 **Tests**: test, test-unit, test-functional, test-coverage
- 🛠️ **Développement**: shell, clean, restart

## 🎯 Fonctionnalités Démontrées

✅ **Transports multiples** (Sync/Async/Failed)
✅ **Messages delayed** (DelayStamp 5000ms)
✅ **Retry strategy** avec backoff exponentiel (3 tentatives)
✅ **Dead Letter Queue** (DLQ)
✅ **Middleware personnalisé** (TimingMiddleware)
✅ **Chaînage de messages**
✅ **Tests unitaires et fonctionnels**

## 📖 Documentation Complète

Toute la documentation se trouve dans `.ai/`:

- [📊 Explications Base de Données](.ai/database_explanation.md) - Pourquoi pas de fixtures?
- [📝 Guide de Démarrage](.ai/setup_guide.md) - Instructions détaillées
- [📐 Spécifications](.ai/project_specifications.md) - Architecture complète

## 📦 Stack Technique

- **PHP** 8.3-fpm-alpine
- **Symfony** 7.0 (Framework, Messenger, Console)
- **PostgreSQL** 16 (Transport Doctrine + DLQ)
- **Redis** 7 (Transport asynchrone)
- **Docker** & Docker Compose
- **PHPUnit** 10

## 🔄 Flux de Traitement

```
DispatchOrderCommand
    ↓
PlaceOrderMessage (sync)
    ↓
PlaceOrderHandler
    ↓ (dispatch avec DelayStamp 5s)
ProcessPaymentMessage (async)
    ↓
ProcessPaymentHandler (retry 3x si échec)
    ↓ (si succès)
SendConfirmationEmailMessage (async)
    ↓
SendConfirmationEmailHandler
```

## 🧪 Tests

```bash
make test              # Tous les tests
make test-unit         # Tests unitaires uniquement
make test-functional   # Tests fonctionnels uniquement
make test-coverage     # Rapport de couverture
```

## 🗄️ Base de Données

**Important**: Ce projet n'utilise **pas de fixtures** car PostgreSQL est utilisé uniquement pour:
- Le transport Messenger synchrone
- La Dead Letter Queue (DLQ)

Les tables sont créées automatiquement par Symfony Messenger.

Voir [.ai/database_explanation.md](.ai/database_explanation.md) pour plus de détails.

## 📝 Licence

MIT
