<?php
if (!defined('ABSPATH')) {
    exit;
}

class IV_Redirect {

    public function __construct() {
        add_action('template_redirect', array($this, 'redirect_non_verified_users'));
    }

    public function redirect_non_verified_users() {
        if (is_user_logged_in() && !is_admin()) {
            $user = wp_get_current_user();
            $verification_status = get_user_meta($user->ID, 'verification_status', true);
            $redirect_page_id = get_option('iv_redirect_no_verificado');
            
            if ($verification_status !== 'verificado' && !is_page($redirect_page_id)) {
                global $wp;
                $redirect_url = $redirect_page_id ? get_permalink($redirect_page_id) : home_url();
                $current_url = home_url(add_query_arg(array(), $wp->request));

                if ($redirect_url && $redirect_url !== $current_url && !headers_sent()) {
                    wp_redirect($redirect_url);
                    exit;
                }
            }
        }
    }
}
