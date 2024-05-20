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
            $user_id = get_current_user_id();
            $verification_status = get_user_meta($user_id, 'verification_status', true);
            $redirect_page_id = get_option('iv_redirect_no_verificado');
            
            if ($verification_status !== 'verified' && !is_page($redirect_page_id)) {
                $redirect_url = $redirect_page_id ? get_permalink($redirect_page_id) : home_url('/');
                if (!headers_sent()) {
                    wp_redirect($redirect_url);
                    exit;
                }
            }
        }
    }
}
?>
