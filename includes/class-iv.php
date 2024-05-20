<?php
if (!defined('ABSPATH')) {
    exit;
}

class Identity_Verification {
    
    public function run() {
        // Inicializar las clases
        $this->load_dependencies();
        $this->init_hooks();
    }

    private function load_dependencies() {
        $this->require_admin_classes();
        $this->require_frontend_classes();
    }

    private function require_admin_classes() {
        require_once plugin_dir_path(__FILE__) . 'admin/class-iv-admin.php';
        require_once plugin_dir_path(__FILE__) . 'class-iv-settings.php';
    }

    private function require_frontend_classes() {
        require_once plugin_dir_path(__FILE__) . 'class-iv-user.php';
        require_once plugin_dir_path(__FILE__) . 'class-iv-redirect.php';
    }

    private function init_hooks() {
        if (is_admin()) {
            new IV_Admin();
            new IV_Settings();
        } else {
            new IV_User();
            new IV_Redirect();
            add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        }
    }

    public function enqueue_styles() {
        wp_enqueue_style('identity-verification', plugin_dir_url(__FILE__) . '../assets/css/identity-verification.css');
    }
}
?>
