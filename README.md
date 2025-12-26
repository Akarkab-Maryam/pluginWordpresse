# WordPress Automation Plugin – Salesforce Report Integration

## 🧩 Description
Ce plugin WordPress a été développé pour automatiser un processus métier critique :
- Enregistrement sécurisé des données utilisateurs
- Connexion à une API externe (Salesforce)
- Téléchargement automatique de rapports
- Envoi d’emails de notification avec pièces jointes

L’objectif principal est de **réduire les tâches manuelles**, **minimiser les erreurs humaines** et **optimiser le temps de traitement**.

---

## 🎯 Problématique métier
Avant ce plugin :
- Les utilisateurs saisissaient les données manuellement
- Les rapports Salesforce étaient téléchargés à la main
- Les notifications étaient envoyées manuellement
- Aucune traçabilité centralisée

➡️ Perte de temps, risque d’erreurs, manque d’automatisation.

---

## ✅ Solution apportée
Ce plugin permet de :
- Enregistrer les informations utilisateurs dans la base de données WordPress
- Se connecter à Salesforce via API
- Télécharger automatiquement les rapports demandés
- Envoyer un email de notification avec le rapport en pièce jointe
- Centraliser la gestion dans l’interface d’administration WordPress

---

## 🧠 Fonctionnalités principales
- 📥 Formulaire sécurisé de saisie des données
- 🔐 Stockage sécurisé en base de données
- 🔗 Intégration API Salesforce
- 📊 Téléchargement automatique de rapports
- 📧 Envoi automatique d’emails de notification
- 🛠 Interface d’administration WordPress dédiée

---

## 🏗 Architecture technique

### Backend
- PHP (Programmation Orientée Objet)
- WordPress Plugin API
- Hooks & Actions
- Sécurisation des formulaires (nonces, sanitization)

### Base de données
- Utilisation de tables WordPress (`wp_options` ou tables personnalisées)
- Stockage structuré des données utilisateurs

### Intégration API
- Connexion à Salesforce via API REST
- Gestion des erreurs et des réponses API
- Téléchargement automatisé des fichiers

### Notifications
- Envoi d’emails via `wp_mail` / PHPMailer
- Pièces jointes automatiques (rapports)

---

## 🧰 Stack technique
- PHP (POO)
- WordPress
- MySQL
- Salesforce API
- PHPMailer
- HTML / CSS (admin UI)

---

## 🔒 Sécurité
- Vérification des accès utilisateurs (roles & capabilities)
- Protection CSRF via nonces
- Nettoyage et validation des données
- Accès restreint à l’interface admin

---

## 📈 Résultats obtenus
- ⏱ Réduction significative du temps de traitement
- ❌ Diminution des erreurs manuelles
- 📬 Notifications automatiques fiables
- 📊 Processus centralisé et traçable

---

## 🚀 Améliorations futures
- Historique détaillé des exécutions
- Logs d’erreurs avancés
- Relance automatique en cas d’échec
- Ajout d’une couche IA pour :
  - Résumé automatique des rapports
  - Détection d’anomalies
- Authentification OAuth avancée

---

## 👩‍💻 Développé par
**Maryam Akarkab**  
Ingénieure en développement informatique & automatisation AI  
Stage pré-embauche – Développeuse AI & Process Automation

---

## 📄 Licence
Projet interne / démonstration professionnelle

<img width="1949" height="1129" alt="image" src="https://github.com/user-attachments/assets/5774873d-ec4a-45bc-b7cf-d8534b7f8732" />

<img width="1082" height="512" alt="image" src="https://github.com/user-attachments/assets/808a990a-27d1-4af8-8197-f58a2eb2e30a" />

<img width="1578" height="608" alt="image" src="https://github.com/user-attachments/assets/b439ceee-2ac2-4a46-9113-6324d74a410f" />





Plugin WordPress avec architecture moderne et séparée utilisant Twig pour le templating.

## Structure du Plugin

```
my-plugin/
├── my-plugin.php              # Fichier principal
├── composer.json              # Dépendances
├── src/
│   ├── Core/
│   │   ├── Plugin.php         # Classe principale
│   │   └── Autoloader.php     # Autoloader personnalisé
│   ├── Admin/
│   │   └── AdminController.php # Contrôleur admin
│   ├── Frontend/
│   │   └── FrontendController.php # Contrôleur frontend
│   ├── Shortcodes/
│   │   └── ShortcodeManager.php # Gestionnaire shortcodes
│   ├── Services/
│   │   ├── Logger.php         # Service de logging
│   │   └── TwigService.php    # Service Twig
│   ├── Models/
│   │   ├── BaseModel.php      # Modèle de base
│   │   └── ExampleModel.php   # Modèle exemple
│   └── Helpers/
│       ├── StringHelper.php   # Helper chaînes
│       └── DateHelper.php     # Helper dates
├── templates/                 # Templates Twig
├── assets/                    # CSS/JS
├── logs/                      # Fichiers de log
└── cache/                     # Cache Twig
```

## Installation

1. Installer les dépendances : `composer install`
2. Activer le plugin dans WordPress
3. Configurer via le menu admin "My Plugin"

## Utilisation

### Shortcodes disponibles

- `[my_plugin_display type="default" count="5"]`
- `[my_plugin_form action="submit"]`

### Logging

Les logs sont automatiquement générés dans le dossier `logs/plugin.log`

### Templates Twig

Les templates sont dans le dossier `templates/` et utilisent la syntaxe Twig standard








