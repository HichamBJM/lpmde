# 🏚️ La Petite Maison de l'Épouvante

Application Symfony 6.4 avec authentification Keycloak OAuth 2.0 et notifications asynchrones via RabbitMQ.

## ✨ Fonctionnalités

- 🔐 **Authentification Keycloak** : Connexion sécurisée via OAuth 2.0 / OpenID Connect
- 🐰 **RabbitMQ** : Notifications asynchrones lors de la connexion
- 👤 **Gestion des utilisateurs** : Profils utilisateurs synchronisés avec Keycloak
- 🎨 **Interface moderne** : Design avec Tailwind CSS
- 📱 **Responsive** : Compatible mobile et desktop

## 🚀 Installation rapide

### Prérequis

- PHP 8.1+
- Composer
- Docker (pour Keycloak et RabbitMQ)

### 1. Installer les dépendances

```bash
composer install
```

### 2. Démarrer les services (Keycloak + RabbitMQ)

```bash
docker-compose -f docker-compose-keycloak.yml up -d
```

Attendez 1-2 minutes que Keycloak démarre complètement.

### 3. Configurer Keycloak

Suivez les instructions détaillées dans [KEYCLOAK_SETUP.md](KEYCLOAK_SETUP.md) :

1. Accédez à http://localhost:8080/admin (admin/admin)
2. Créez un client OAuth `symfony-app`
3. Copiez le Client Secret
4. Créez un utilisateur de test

### 4. Configurer Symfony

Copiez et modifiez le fichier `.env` :

```bash
cp .env .env.local
```

Mettez à jour dans `.env.local` :

```env
KEYCLOAK_URL=http://localhost:8080
KEYCLOAK_REALM=master
KEYCLOAK_CLIENT_ID=symfony-app
KEYCLOAK_CLIENT_SECRET=YOUR_CLIENT_SECRET_HERE
KEYCLOAK_REDIRECT_URI=http://localhost:8000/login/keycloak/callback
```

### 5. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n
```

### 6. Démarrer le worker RabbitMQ

Dans un terminal séparé :

```bash
php bin/console messenger:consume async -vv
```

### 7. Démarrer le serveur Symfony

```bash
symfony serve
```

Ou :

```bash
php -S localhost:8000 -t public
```

## 🎮 Utilisation

1. Visitez http://localhost:8000
2. Cliquez sur **"Connexion"** dans le header
3. Cliquez sur **"Se connecter avec Keycloak"**
4. Connectez-vous avec vos identifiants Keycloak
5. Une notification apparaîtra sur la page d'accueil
6. Le worker RabbitMQ affichera la notification dans le terminal

## 📁 Structure du projet

```
src/
├── Controller/
│   ├── HomeController.php          # Page d'accueil
│   ├── LoginController.php         # Test RabbitMQ
│   └── SecurityController.php      # Authentification Keycloak
├── Entity/
│   └── User.php                    # Entité utilisateur
├── Message/
│   ├── GhostAlert.php              # Message démo
│   └── UserLoginNotification.php   # Notification de connexion
├── MessageHandler/
│   ├── GhostAlertHandler.php
│   └── UserLoginNotificationHandler.php
├── Repository/
│   └── UserRepository.php
└── Service/
    └── KeycloakService.php         # Service OAuth Keycloak

templates/
├── security/
│   ├── login_keycloak.html.twig   # Page de connexion
│   └── profile.html.twig           # Profil utilisateur
└── partials/
    └── _header.html.twig           # Header avec menu utilisateur
```

## 🔗 Routes disponibles

- `/` - Page d'accueil
- `/login/keycloak` - Page de connexion Keycloak
- `/login/keycloak/callback` - Callback OAuth
- `/profile` - Profil utilisateur (nécessite connexion)
- `/logout` - Déconnexion
- `/test-rabbit` - Test RabbitMQ (50 messages)

## 🛒 Partie Commandes

Cette section couvre le cycle de vie d'une commande e-commerce :

### 1) Panier
- Ajout/retrait de produits
- Mise à jour des quantités
- Calcul du total et validation du panier

### 2) Commande
- Transformation du panier en commande
- Génération d'un identifiant unique de commande
- Persistance des lignes de commande et des informations client

### 3) Statuts de commande
- `BROUILLON` : panier en préparation
- `EN_ATTENTE_PAIEMENT` : commande créée, paiement non confirmé
- `PAYEE` : paiement validé
- `EN_PREPARATION` : préparation logistique en cours
- `EXPEDIEE` : colis remis au transporteur
- `LIVREE` : commande livrée
- `ANNULEE` : commande annulée

### 4) Orchestration d'expédition
- Déclenchement asynchrone de l'expédition après validation de paiement
- Suivi des événements via messages (ex: RabbitMQ / Messenger)
- Mise à jour automatique du statut selon les retours transporteur
- Notification utilisateur à chaque changement d'état clé

> Recommandation technique : centraliser les transitions de statuts dans un service dédié
> (workflow/state machine) pour garantir la cohérence métier et la traçabilité.


## 🔄 CI/CD

Le pipeline GitHub Actions (`.github/workflows/ci-cd.yml`) exécute :

- **Install** : installation des dépendances Composer
- **Tests** : unitaires, fonctionnels, e2e
- **SAST** : lint PHP, `composer audit`, Trivy filesystem
- **DAST** : scan baseline OWASP ZAP contre l'application démarrée en local CI
- **Qualité** : audit SonarCloud (si `SONAR_TOKEN` est configuré)
- **Release** : build/push image Docker GHCR
- **Deploy** : staging (branche `develop`) et production (`main`/`master`)

## 📚 Documentation

- [KEYCLOAK_SETUP.md](KEYCLOAK_SETUP.md) - Configuration détaillée de Keycloak
- [DOCKER_QUICK_START.md](DOCKER_QUICK_START.md) - Démarrage rapide avec Docker
- [DEPLOYMENT.md](DEPLOYMENT.md) - Guide de déploiement

## 🐛 Dépannage

### Keycloak ne démarre pas
```bash
docker-compose -f docker-compose-keycloak.yml logs -f keycloak
```

### RabbitMQ ne reçoit pas les messages
- Vérifiez que le worker tourne : `php bin/console messenger:consume async -vv`
- Vérifiez la connexion : http://localhost:15672 (guest/guest)

### Erreur "Invalid redirect_uri"
- Vérifiez que l'URL dans Keycloak correspond exactement à celle dans `.env`

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou un pull request.

## 📄 Licence

Propriétaire

## 🛠️ Technologies utilisées

- **Framework** : Symfony 6.4
- **Authentification** : Keycloak OAuth 2.0
- **Messaging** : RabbitMQ + Symfony Messenger
- **Base de données** : SQLite (dev) / PostgreSQL (prod)
- **Frontend** : Tailwind CSS
- **Containerisation** : Docker & Docker Compose

---

🎉 **Profitez de votre application avec authentification Keycloak et notifications RabbitMQ !**
