<?php
/*
Plugin Name: Identity Verification
Description: Verifica la identidad de los usuarios y muestra contenido según el estado de verificación.
Version: 1.0
Author: Yasiel Hernández Portal
Text Domain: identity-verification
*/

// Evitar el acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

// Cargar archivos de idiomas
function iv_load_textdomain() {
    load_plugin_textdomain('identity-verification', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'iv_load_textdomain');

// Incluir los archivos necesarios
require_once plugin_dir_path(__FILE__) . 'includes/class-iv-admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-iv-user.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-iv-redirect.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-iv-settings.php';

// Inicializar las clases
function iv_init() {
    new IV_Admin();
    new IV_User();
    new IV_Redirect();
    new IV_Settings();
}
add_action('plugins_loaded', 'iv_init');

// Registrar la función de desinstalación
register_uninstall_hook(__FILE__, 'iv_uninstall');

function iv_uninstall() {
    if (!defined('WP_UNINSTALL_PLUGIN')) {
        exit;
    }

    // Eliminar opciones del plugin
    delete_option('iv_redirect_no_verificado');
    delete_option('iv_content_pendiente');
    delete_option('iv_redirect_verificado');
    delete_option('iv_content_verificado');

    // Obtener todos los usuarios y eliminar los metadatos relacionados con la verificación de identidad
    $users = get_users(array('fields' => array('ID')));

    foreach ($users as $user) {
        delete_user_meta($user->ID, 'identity_verification');
        delete_user_meta($user->ID, 'identity_verification_file');
        delete_user_meta($user->ID, 'verification_status');
    }
}
