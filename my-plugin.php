<?php
/**
 * Plugin Name: Plugin Maryam
 * Description: Plugin WordPress avec architecture séparée et Twig
 * Version: 1.0.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MY_PLUGIN_VERSION', '1.0.0');

require_once MY_PLUGIN_PATH . 'vendor/autoload.php';
require_once MY_PLUGIN_PATH . 'src/Core/Autoloader.php';

use MyPlugin\Core\Plugin;

// Initialisation du plugin
add_action('plugins_loaded', function() {
    Plugin::getInstance()->init();
});

// Activation/Désactivation
register_activation_hook(__FILE__, [Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [Plugin::class, 'deactivate']);