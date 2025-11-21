# PROMPT : DÉMONSTRATION AVANCÉE DE SYMFONY MESSENGER

## Objectif

Créer un projet Symfony nommé **AdvancedMessengerDemo** qui implémente un **flux de traitement asynchrone** pour démontrer les fonctionnalités avancées de Symfony Messenger.

## Localisation

Créer le projet dans : `/advanced-messenger-demo`

## Infrastructure

Utiliser **Docker Compose** avec :
- Conteneur PHP avec extensions PostgreSQL et Redis
- Conteneur PostgreSQL pour transport synchrone et DLQ
- Conteneur Redis pour transport asynchrone

## Fonctionnalités à Démontrer

### 1. Transports Multiples

Configurer 3 transports Messenger :
- **Transport synchrone** (Doctrine/PostgreSQL)
- **Transport asynchrone** (Redis) avec retry strategy
- **Dead Letter Queue** (Doctrine/PostgreSQL) pour messages en échec permanent

### 2. Flux de Traitement avec Chaînage de Messages

Implémenter un flux complet avec **3 messages chaînés** démontrant :

**Premier message** (synchrone) :
- Déclenche le flux de traitement
- Dispatche le deuxième message avec un **délai de 5 secondes**

**Deuxième message** (asynchrone) :
- Simule un traitement avec **possibilité d'échec aléatoire**
- En cas d'échec : **retry automatique** (3 tentatives max)
- Après 3 échecs : envoi vers **Dead Letter Queue**
- En cas de succès : dispatche le troisième message

**Troisième message** (asynchrone) :
- Finalise le traitement
- Simule une opération finale

### 3. Delayed Messages

Le deuxième message doit être dispatché avec un **délai de 5 secondes** après le premier message.

### 4. Retry Strategy

Configurer une stratégie de retry avec :
- Maximum 3 tentatives
- Délai croissant entre les tentatives (backoff exponentiel)
- Envoi vers DLQ après épuisement des tentatives

### 5. Middleware Personnalisé

Créer un middleware qui :
- Mesure le temps d'exécution de chaque message
- Log le début et la fin du traitement
- Log la durée en millisecondes
- Gère les erreurs avec logging approprié

### 6. Commande Console

Créer une commande console qui :
- Déclenche le flux de traitement
- Génère des données de test aléatoires
- Dispatche le premier message
- Affiche un message de confirmation

## Tests

Fournir des tests pour garantir :
- Le deuxième message est dispatché avec le bon délai (5 secondes)
- La commande console fonctionne correctement
- Le chaînage des messages est correct

## Règles de Tests & Debugging

### 🎯 Principes de Mocking

#### 1. Services Custom (Métier Projet)
*   **Règle** : 🚫 **Ne PAS mocker** les services métier du projet (`App\Service\...`) sauf nécessité absolue.
*   **Pourquoi** : On veut tester l'intégration réelle et le comportement de la chaîne métier.
*   **Exception** : Si le service fait des appels externes lourds (API tierce, envoi mail réel), utiliser un Mock ou un Stub.

#### 2. Services Symfony / Vendor
*   **Règle** : ✅ **Mocker systématiquement** les services du framework (`RequestStack`, `MailerInterface`, `EventDispatcher`, etc.).
*   **Pourquoi** : On ne teste pas le framework, on suppose qu'il fonctionne. On veut isoler notre logique.

### 🧪 Types de Tests

*   **Unitaires** : Isolation maximale. Mocks autorisés pour tout ce qui est externe à la classe testée.
*   **Intégration / Fonctionnels** : Utiliser le conteneur de services (`KernelTestCase` / `WebTestCase`) pour valider le câblage.

### 🕵️ Philosophie de Debugging (Root Cause)

**IMPORTANT** : Si un test échoue après une modification de code :

1.  🛑 **Ne JAMAIS modifier le test** pour le faire passer (sauf si la spécification a changé).
2.  🔍 Chercher la **cause racine** du problème dans le code modifié.
3.  🛡️ Le test est le **gardien de la vérité** ; s'il échoue, c'est que le code a introduit une régression.

## Outils de Développement

### Makefile

Fournir un **Makefile** avec des commandes pour :
- Installation complète du projet
- Démarrage/arrêt de l'infrastructure
- Dispatch d'une commande de test
- Consommation des messages asynchrones
- Consommation de la Dead Letter Queue
- Exécution des tests
- **Qualité de Code** :
    - `make cs-fixer` : Correction du style
    - `make phpstan` : Analyse statique
    - `make rector` : Refactoring auto
    - `make ci` : Lance toute la chaîne (cs-fixer + phpstan + tests)
- Simulation du CI en local
- Structure du help : Utiliser un système automatique avec marqueurs ## pour les sections, détection automatique par grep/awk, emojis cohérents par section, differentes couleurs pour les sections et les commandes.

### Qualité de Code et CI/CD

Configurer les outils de qualité de code suivants :

**Outils Requis** :
- **PHP-CS-Fixer** : Formatage automatique du code (PSR-12)
- **PHPStan** : Analyse statique (niveau max)
- **Rector** : Modernisation et refactoring automatique
- **PHPMD** : Détection de code smell

**Configuration CI/CD** :
Fournir une configuration GitHub Actions (ou GitLab CI) qui :
- Vérifie le formatage du code
- Exécute l'analyse statique
- Lance les tests
- Vérifie la qualité globale

**Fichiers de Configuration** :
Créer les fichiers de configuration pour chaque outil avec des règles strictes mais adaptées au projet.

## Documentation

**IMPORTANT** : Tous les fichiers de documentation doivent être créés dans le répertoire `.ai_documents/` à la racine du projet.

La documentation doit inclure :
- **Spécifications du projet** : Architecture, flux de traitement, transports, choix techniques
- **Documentation Fonctionnelle** : Expliquer le scénario métier (ex: Commande -> Paiement -> Email) de manière simple pour qu'un non-technicien comprenne ce que fait l'application.
- **Guide de démarrage** : Installation, utilisation, scénarios de test
- **Technologies utilisées** : Stack technique avec versions choisies
- **Règles de qualité** : Outils CI/CD, règles appliquées, configuration détaillée
- **Ce prompt** : Pour référence future

Le README principal doit contenir :
- Description claire du projet et de ses objectifs
- Instructions d'installation (automatique et manuelle)
- Instructions d'utilisation avec exemples
- Explication du flux de traitement
- Commandes principales du Makefile
- Structure du projet
- Badges de qualité (CI/CD status, PHPStan level, coverage)

## Bonnes Pratiques

Respecter les conventions Symfony et les bonnes pratiques PHP :
- **Séparation des responsabilités** : SOLID principles
- **Messages immutables** : Classes readonly
- **Tests complets** : Couverture des cas critiques
- **Documentation claire** : Explications et exemples
- **Code propre** : Respect des standards PSR-12
- **Analyse statique** : Code sans erreurs PHPStan niveau max
- **CI/CD** : Validation automatique de la qualité

## Autonomie de l'IA

**IMPORTANT** : L'IA a une **autonomie complète** pour :

### Exécution Sans Confirmation

- ✅ **Commandes de lecture** : `ls`, `cat`, `grep`, `find`, etc. → Exécution immédiate
- ✅ **Commandes de vérification** : `wc`, `tree`, `diff`, etc. → Exécution immédiate
- ✅ **Modifications demandées explicitement** : Si l'utilisateur demande une modification, c'est une confirmation implicite → Exécution immédiate

### Décisions Techniques

- ✅ **Choix des versions** : PHP, Symfony, PostgreSQL, Redis, etc.
- ✅ **Noms de classes** : Messages, Handlers, Commands, etc.
- ✅ **Noms de fichiers** : Structure et organisation du code
- ✅ **Configuration** : Paramètres optimaux pour Messenger, retry, etc.
- ✅ **Implémentation** : Détails du code, exceptions, logging, etc.
- ✅ **Contexte métier** : Choix du domaine métier pour la démonstration
- ✅ **Conventions de code** : Typage strict, formatage, etc.

### Documentation des Choix

L'IA doit **expliquer ses choix** dans la documentation :
- Pourquoi ces versions ?
- Pourquoi ce contexte métier ?
- Pourquoi cette configuration ?
- Quels sont les avantages ?



## Vérifications Finales

Avant de livrer le projet, l'IA doit effectuer les vérifications suivantes :

1. **Installation** : Simuler l'installation complète (`make setup`) et corriger automatiquement tout problème (conflits de ports, dépendances, etc.).
2. **Tests** : Exécuter `make test` et s'assurer que **tous** les tests passent (vert). Corriger le code si nécessaire, pas les tests.
3. **Documentation** : Vérifier que tous les liens, commandes et informations dans la documentation sont valides et à jour.
4. **Flux Fonctionnel** : Vérifier manuellement (via `make dispatch` et `make consume-async`) que le comportement synchrone/asynchrone est respecté (pas de blocage en base).

## Rendu Attendu

Un projet Symfony complet et fonctionnel qui :
1. Démontre toutes les fonctionnalités avancées de Messenger
2. Est facile à installer et à utiliser (via Makefile)
3. Contient des tests pour valider le comportement
4. Est bien documenté avec explications claires des choix techniques
5. Respecte les standards de qualité (CI/CD configuré)
6. Peut servir de référence pour étudier Messenger
7. Est **production-ready** avec tous les outils de qualité configurés

Le projet doit être **prêt à l'emploi** : un développeur doit pouvoir cloner, installer, tester et comprendre immédiatement.
