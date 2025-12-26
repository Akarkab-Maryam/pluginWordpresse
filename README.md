# My Plugin WordPress

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

Les templates sont dans le dossier `templates/` et utilisent la syntaxe Twig standard.