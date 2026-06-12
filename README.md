# 🇸🇳 Bibliothèque Numérique du Sénégal — Panneau d'Administration

> Un système de gestion moderne, fluide et sécurisé pour administrer les livres, les étudiants et les prêts d'une bibliothèque universitaire sénégalaise.

---

## 🌟 Fonctionnalités Clés

* **📊 Tableau de Bord Dynamique :** Visualisez instantanément les statistiques importantes (nombre total de livres, étudiants enregistrés, prêts actifs et retards).
* **📚 Gestion du Catalogue (Livres) :** CRUD complet permettant d'ajouter, modifier, rechercher et supprimer des titres (avec gestion automatique des stocks de copies disponibles).
* **🎓 Suivi des Étudiants :** Gestion simple des profils étudiants avec recherche et association automatique aux emprunts.
* **📖 Gestion des Prêts & Retours :** Enregistrement des transactions de prêts avec détection automatique des retours en retard.
* **🛡️ Sécurité Renforcée :** Protection globale contre les failles CSRF sur toutes les soumissions de formulaires et sécurisation des actions destructives par requête POST.
* **🎨 Interface Élégante :** Thème sombre moderne inspiré du Sénégal avec des arrière-plans animés par p5.js (particules en mouvement et livres flottants).

---

## 🛠️ Stack Technique

* **Serveur / Logique :** PHP 7.4+ (requiert les extensions `pdo` et `pdo_mysql`)
* **Base de données :** MySQL / MariaDB
* **Design & Animations :** Vanilla CSS, Bootstrap 5.3, Font Awesome 6, p5.js
* **Sécurité :** Authentification par session PHP, hachage de mot de passe (`password_hash`), jetons CSRF uniques par formulaire.

---

## 🚀 Guide de Démarrage Rapide

### 📋 Prérequis
* PHP installé sur votre machine (ex. via Laragon, XAMPP, ou en ligne de commande).
* Serveur de base de données MySQL actif.

### 📥 1. Importer la Base de Données
Créez la base de données et importez les données de test à l'aide du fichier de dump fourni :
```bash
mysql -u root -p < db.sql
```
*(Le script va créer automatiquement la base `library_senegal` et insérer les tables nécessaires ainsi que 94 livres et utilisateurs fictifs).*

### 🔑 2. Configuration
Ouvrez le fichier `config.php` et ajustez les informations de connexion à votre base de données MySQL si nécessaire :
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'library_senegal');
define('DB_USER', 'root'); // Votre utilisateur
define('DB_PASS', '');     // Votre mot de passe
```

### 💻 3. Lancer le Serveur Local
Démarrez le serveur interne de PHP depuis la racine du projet :
```bash
php -S localhost:8000
```

### 🌐 4. Accéder à l'Application
Rendez-vous sur votre navigateur préféré à l'adresse suivante :
👉 **[http://localhost:8000](http://localhost:8000)**

---

## 🔐 Identifiants de Test (Se connecter)

Utilisez le compte administrateur pré-configuré dans la base de données :

* **Adresse Email :** `admin@senlibrary.edu`
* **Mot de passe :** `admin123`

---

## 📁 Structure Globale du Projet

```
Projet PHP/
├── assets/
│   ├── css/style.css     # Charte graphique & Glassmorphism
│   └── js/
│       ├── dashboard.js  # Animation canvas du Dashboard (p5.js)
│       └── p5-login.js   # Animation canvas de Connexion (p5.js)
├── partials/
│   └── nav.php           # Barre de navigation partagée
├── config.php            # Connexion PDO & Fonctions de sécurité (CSRF, Auth)
├── index.php             # Page d'accueil & Connexion sécurisée
├── dashboard.php         # Tableau de bord principal
├── books.php             # Gestion des livres (CRUD)
├── students.php          # Gestion des profils étudiants (CRUD)
├── borrowings.php        # Gestion des prêts & retours
├── logout.php            # Déconnexion propre
├── db.sql                # Script d'import de la base de données
└── README.md             # Ce fichier
```

---

## 🤝 Contribution & Git

Pour cloner et pousser vos modifications sur votre propre dépôt GitHub :
```bash
# Initialiser le dépôt
git init

# Ajouter les fichiers
git add .
git commit -m "Initial commit: Sénégal Digital Library fonctionnel et sécurisé"

# Lier le dépôt distant (remplacez l'URL)
git remote add origin https://github.com/votre-username/nom-du-depot.git
git branch -M main
git push -u origin main
```
