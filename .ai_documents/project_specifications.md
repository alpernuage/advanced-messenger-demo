# Advanced Messenger Demo - Spécifications du Projet

## Vue d'ensemble

Ce projet démontre les fonctionnalités avancées de Symfony Messenger à travers un flux de traitement de commande asynchrone.

## 🛍️ Scénario Métier (Fonctionnel)

Le projet simule une commande e-commerce en 3 étapes chaînées :

1.  **Commande (Synchrone)** : L'utilisateur passe commande. C'est immédiat. Le système prépare la suite et demande un paiement avec un **délai de 5 secondes**.
2.  **Paiement (Asynchrone + Délai)** :
    *   Le système attend 5 secondes.
    *   Il tente le paiement (avec **20% de chance d'échec** pour tester la robustesse).
    *   En cas d'échec : il réessaie automatiquement (Retry).
3.  **Confirmation (Asynchrone)** :
    *   Si le paiement réussit, un email de confirmation est envoyé.

## Architecture

### Messages

1. **PlaceOrderMessage** (Synchrone)
   - Propriétés: `orderId`, `amount`
   - Transport: `sync` (Doctrine)
   - Handler: `PlaceOrderHandler`

2. **ProcessPaymentMessage** (Asynchrone avec retry)
   - Propriétés: `orderId`
   - Transport: `async` (Redis)
   - Handler: `ProcessPaymentHandler`
   - Delayed: 5 secondes après dispatch

3. **SendConfirmationEmailMessage** (Asynchrone)
   - Propriétés: `orderId`
   - Transport: `async` (Redis)
   - Handler: `SendConfirmationEmailHandler`

### Flux de Traitement

```
DispatchOrderCommand
    ↓
PlaceOrderMessage (sync)
    ↓
PlaceOrderHandler
    ↓ (dispatch avec DelayStamp 5000ms)
ProcessPaymentMessage (async)
    ↓
ProcessPaymentHandler (20% échec, retry 3x)
    ↓ (si succès)
SendConfirmationEmailMessage (async)
    ↓
SendConfirmationEmailHandler
```

### Transports

- **sync**: Doctrine (PostgreSQL) - Messages synchrones
- **async**: Redis - Messages asynchrones avec retry
- **failed**: Doctrine (PostgreSQL) - Dead Letter Queue

### Stratégie de Retry

- Max retries: 3
- Délai initial: 1000ms
- Multiplicateur: 2 (1s, 2s, 4s)
- Après 3 échecs: envoi vers DLQ (transport `failed`)

### Middleware Personnalisé

**TimingMiddleware**: Mesure et log le temps d'exécution de chaque message.

## Infrastructure Docker

- **php**: PHP 8.3-fpm-alpine avec extensions pdo_pgsql et redis
- **postgres**: PostgreSQL 16 pour Doctrine transport et DLQ
- **redis**: Redis 7 pour transport asynchrone

## Tests

### Tests Unitaires

- `PlaceOrderHandlerTest`: Vérifie le dispatch avec DelayStamp de 5000ms

### Tests Fonctionnels

- `DispatchOrderCommandTest`: Vérifie l'exécution de la commande console

## Commandes Utiles

Voir le `Makefile` pour toutes les commandes disponibles:

- `make setup`: Installation complète
- `make dispatch`: Dispatcher une commande de test
- `make consume-async`: Consommer les messages asynchrones
- `make consume-failed`: Consommer les messages de la DLQ
- `make test`: Exécuter les tests PHPUnit
