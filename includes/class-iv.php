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
        require_once plugin_dir_path(__FILE__) . 'admin/class-iv-admin.php';
        require_once plugin_dir_path(__FILE__) . 'class-iv-user.php';
        require_once plugin_dir_path(__FILE__) . 'class-iv-redirect.php';
        require_once plugin_dir_path(__FILE__) . 'class-iv-settings.php';
    }

    private function init_hooks() {
        new IV_Admin();
        new IV_User();
        new IV_Redirect();
        new IV_Settings();
    }
}
