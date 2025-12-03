# DLP_Prism

Application web de gestion des stages développée avec Symfony 6.4, permettant la gestion complète des stages étudiants, entreprises, tuteurs et employés.

![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-6.4-000000?style=for-the-badge&logo=symfony&logoColor=white)
![Doctrine](https://img.shields.io/badge/Doctrine-3.3-FC6A31?style=for-the-badge&logo=doctrine&logoColor=white)
![Twig](https://img.shields.io/badge/Twig-3.x-BAE67C?style=for-the-badge&logo=twig&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-2.x-885630?style=for-the-badge&logo=composer&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

## 🏗️ Architecture

### Structure du projet

```
DLP_Prism/
├── 📁 bin/                          # Scripts console
│   └── console                     # Console Symfony
│
├── 📁 config/                       # Configuration de l'application
│   ├── 📁 packages/                # Configuration des bundles
│   │   ├── cache.yaml             # Configuration du cache
│   │   ├── doctrine.yaml          # Configuration Doctrine ORM
│   │   ├── doctrine_migrations.yaml
│   │   ├── framework.yaml         # Configuration du framework
│   │   ├── routing.yaml           # Configuration du routage
│   │   ├── security.yaml          # Configuration de la sécurité
│   │   ├── twig.yaml              # Configuration Twig
│   │   └── web_profiler.yaml      # Profiler de développement
│   ├── 📁 routes/                  # Définition des routes
│   ├── bundles.php                # Enregistrement des bundles
│   ├── services.yaml              # Configuration des services
│   └── routes.yaml                # Routes principales
│
├── 📁 migrations/                   # Migrations de base de données
│
├── 📁 public/                       # Répertoire web public
│   ├── index.php                  # Point d'entrée de l'application
│   ├── 📁 css/                     # Feuilles de style
│   │   ├── normalize.css
│   │   ├── reset.css
│   │   └── style.css
│   └── 📁 assets/                  # Assets statiques (images, icons)
│
├── 📁 src/                          # Code source de l'application
│   ├── Kernel.php                 # Noyau Symfony
│   │
│   ├── 📁 Command/                 # Commandes console
│   │   └── CreateUserCommand.php  # Création d'utilisateurs
│   │
│   ├── 📁 Controller/              # Contrôleurs MVC
│   │   ├── AdminController.php    # Gestion admin (users, pays, spécialités)
│   │   ├── AuthController.php     # Authentification (login, register, logout)
│   │   ├── DashboardController.php # Tableau de bord & statistiques
│   │   ├── EmployeController.php  # CRUD Employés/Tuteurs
│   │   ├── EntrepriseController.php # CRUD Entreprises
│   │   ├── EtudiantController.php # CRUD Étudiants
│   │   └── StageController.php    # CRUD Stages
│   │
│   ├── 📁 Entity/                  # Entités Doctrine (Modèle)
│   │   ├── User.php               # Utilisateurs de l'app
│   │   ├── Entreprise.php         # Entreprises partenaires
│   │   ├── Etudiant.php           # Étudiants
│   │   ├── Stage.php              # Stages
│   │   ├── Personne.php           # Employés/Tuteurs en entreprise
│   │   ├── Specialite.php         # Spécialités/domaines
│   │   ├── Profil.php             # Profils professionnels
│   │   └── Pays.php               # Pays
│   │
│   └── 📁 Repository/              # Repositories Doctrine (Accès données)
│       ├── UserRepository.php
│       ├── EntrepriseRepository.php
│       ├── EtudiantRepository.php
│       ├── StageRepository.php
│       ├── PersonneRepository.php
│       ├── SpecialiteRepository.php
│       ├── ProfilRepository.php
│       └── PaysRepository.php
│
├── 📁 templates/                    # Templates Twig (Vues)
│   ├── base.html.twig             # Template de base
│   ├── 📁 auth/                    # Templates d'authentification
│   │   ├── login.html.twig
│   │   └── register.html.twig
│   ├── 📁 dashboard/               # Templates dashboard
│   │   ├── index.html.twig        # Dashboard principal
│   │   ├── admin.html.twig        # Panel admin
│   │   ├── employe.html.twig      # Gestion employés
│   │   ├── entreprise.html.twig   # Gestion entreprises
│   │   ├── etudiant.html.twig     # Gestion étudiants
│   │   └── stage.html.twig        # Gestion stages
│   └── 📁 layouts/                 # Layouts réutilisables
│       ├── auth.html.twig
│       └── dashboard.html.twig
│
├── composer.json                    # Dépendances PHP
└── LICENSE                          # Licence du projet
```

## 🚀 Installation

### Prérequis

- PHP 8.1 ou supérieur
- Composer
- Serveur de base de données (MySQL, PostgreSQL, etc.)
- Extensions PHP : ctype, iconv

### Étapes d'installation

1. **Cloner le repository**

```bash
git clone https://github.com/Jul0P/DLP_Prism.git
cd DLP_Prism
```

2. **Installer les dépendances**

```bash
composer install
```

3. **Configurer la base de données**

Créer un fichier `.env.local` à la racine du projet :

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/dlp_prism?serverVersion=8.0"
```

Ou pour PostgreSQL :

```env
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/dlp_prism?serverVersion=16&charset=utf8"
```

4. **Créer la base de données**

```bash
php bin/console doctrine:database:create
```

5. **Exécuter les migrations**

```bash
php bin/console doctrine:migrations:migrate
```

6. **Créer les utilisateurs par défaut** (optionnel)

```bash
php bin/console app:create-user
```

Cette commande crée :

- Un administrateur : `bocba@cba.fr` / `bocba@cba.fr`
- Un utilisateur : `focba@cba.fr` / `focba@cba.fr`

7. **Démarrer le serveur de développement**

```bash
symfony server:start
```

Ou avec PHP :

```bash
php -S localhost:8000 -t public/
```

8. **Accéder à l'application**

Ouvrir votre navigateur à l'adresse : `http://localhost:8000`

### Routes

Routes principales :

- `/login` - Connexion
- `/register` - Inscription
- `/logout` - Déconnexion
- `/dashboard` - Tableau de bord
- `/` - Liste des entreprises
- `/etudiant` - Gestion des étudiants
- `/employe` - Gestion des employés
- `/stage` - Gestion des stages
- `/admin` - Administration (réservé aux admins)

## 📝 Commandes utiles

### Doctrine

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Créer une migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Vider le cache
php bin/console cache:clear
```

### Symfony

```bash
# Créer un contrôleur
php bin/console make:controller

# Créer une entité
php bin/console make:entity

# Lister les routes
php bin/console debug:router

# Démarrer le serveur
symfony server:start
```
