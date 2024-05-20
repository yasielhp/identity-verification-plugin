<?php
/*
Plugin Name: Identity Verification
Description: Verifica la identidad de los usuarios y muestra contenido según el estado de verificación.
Version: 1.0
Author: Yasiel Hernández Portal
Text Domain: identity-verification
Domain Path: /languages
*/

// Evitar el acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

// Inicializar el plugin
function iv_init() {
    // Cargar archivos de idiomas
    load_plugin_textdomain('identity-verification', false, dirname(plugin_basename(__FILE__)) . '/languages');
    
    // Incluir los archivos necesarios
    require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-iv.php';

    // Inicializar el plugin
    $plugin = new Identity_Verification();
    $plugin->run();
}
add_action('plugins_loaded', 'iv_init');
?>
