# HyperChat

HyperChat est une application de messagerie instantanée moderne développée avec Symfony et Twilio, permettant aux utilisateurs de communiquer en temps réel.

## 🚀 Fonctionnalités

- **Messagerie en temps réel** : Communication instantanée entre utilisateurs
- **Gestion des amis** : Système de demandes d'amis et liste de contacts
- **Interface moderne** : Design responsive avec Bootstrap 5
- **Notifications** : Indicateurs de messages non lus
- **Sécurité** : Authentification et autorisation des utilisateurs
- **Intégration Twilio** : Utilisation de l'API Twilio pour la messagerie en temps réel

## 🛠️ Technologies utilisées

- **Backend** :
  - PHP 8.2
  - Symfony 6.4
  - Doctrine ORM
  - Twilio API

- **Frontend** :
  - Bootstrap 5
  - JavaScript
  - Twig

- **Base de données** :
  - MySQL 8.0

- **Infrastructure** :
  - Docker
  - Docker Compose

## 📋 Prérequis

- Docker
- Docker Compose
- Git

## 🚀 Installation

1. **Cloner le repository**
   ```bash
   git clone [URL_DU_REPO]
   cd HyperChat
   ```

2. **Configurer les variables d'environnement**
   ```bash
   cp .env .env.local
   ```
   Modifier le fichier `.env.local` avec vos configurations :
   - `DATABASE_URL`
   - `TWILIO_ACCOUNT_SID`
   - `TWILIO_AUTH_TOKEN`
   - `TWILIO_SERVICE_SID`

3. **Lancer les conteneurs Docker**
   ```bash
   docker-compose up -d
   ```

4. **Installer les dépendances**
   ```bash
   docker exec hyperchat-symfony-1 composer install
   ```

5. **Configurer la base de données**
   ```bash
   docker exec hyperchat-symfony-1 php bin/console doctrine:database:create
   docker exec hyperchat-symfony-1 php bin/console doctrine:migrations:migrate
   ```

6. **Créer un utilisateur administrateur**
   ```bash
   docker exec hyperchat-symfony-1 php bin/console app:create-admin
   ```

## 🏃‍♂️ Exécution

1. **Démarrer l'application**
   ```bash
   docker-compose up -d
   ```

2. **Accéder à l'application**
   - Frontend : http://localhost:8000
   - Base de données : localhost:3307 (MySQL)

## 📁 Structure du projet

```
HyperChat/
├── config/                 # Configuration Symfony
├── migrations/            # Migrations de base de données
├── public/               # Point d'entrée public
├── src/                  # Code source PHP
│   ├── Controller/      # Contrôleurs
│   ├── Entity/         # Entités Doctrine
│   ├── Repository/     # Repositories
│   └── Service/        # Services
├── templates/           # Templates Twig
├── .env                 # Variables d'environnement
├── docker-compose.yml   # Configuration Docker
└── README.md           # Documentation
```

## 🔧 Configuration

### Variables d'environnement

- `DATABASE_URL` : URL de connexion à la base de données
- `TWILIO_ACCOUNT_SID` : Identifiant du compte Twilio
- `TWILIO_AUTH_TOKEN` : Token d'authentification Twilio
- `TWILIO_SERVICE_SID` : Identifiant du service Twilio

### Configuration Docker

Le projet utilise deux conteneurs :
- `hyperchat-symfony-1` : Application Symfony
- `hyperchat-database-1` : Base de données MySQL

## 🤝 Contribution

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add some AmazingFeature'`)
4. Push sur la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👥 Auteurs

- Votre nom - Développeur principal

## 🙏 Remerciements

- Symfony
- Twilio
- Bootstrap
- Docker
