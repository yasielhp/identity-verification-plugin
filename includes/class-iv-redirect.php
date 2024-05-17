<?php
if (!defined('ABSPATH')) {
    exit;
}

class IV_Redirect {

    public function __construct() {
        // Agrega el gancho 'template_redirect' y llama a la función 'redirect_non_verified_users'
        add_action('template_redirect', array($this, 'redirect_non_verified_users'));
    }

    public function redirect_non_verified_users() {
        // Verifica si el usuario está conectado y no es un administrador
        if (is_user_logged_in() && !is_admin()) {
            // Obtiene el usuario actual
            $user = wp_get_current_user();
            $user_roles = $user->roles;

            // Obtiene el estado de verificación del usuario
            $verification_status = get_user_meta($user->ID, 'verification_status', true);

            // Obtiene el ID de la página de redirección no verificada desde la configuración
            $redirect_page_id = get_option('iv_redirect_no_verificado');
            
            // Redirección según el estado de verificación
            if ($verification_status !== 'verificado' && !is_page($redirect_page_id)) {
                
                // Obtiene la URL de redirección
                $redirect_url = $redirect_page_id ? get_permalink($redirect_page_id) : home_url(add_query_arg(array(), $wp->request));

                // Definir la variable global $wp
                global $wp;
                $current_url = home_url(add_query_arg(array(), $wp->request));

                // Verificar que la URL de redirección es válida y no redirige a la misma página actual
                if ($redirect_url && $redirect_url !== $current_url && !headers_sent()) {
                    // Redirige al usuario a la URL especificada
                    wp_redirect($redirect_url);
                    exit;
                }
            }
    
        }
    }
}
