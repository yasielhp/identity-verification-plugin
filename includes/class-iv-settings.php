<?php
if (!defined('ABSPATH')) {
    exit;
}

class IV_Settings {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page() {
        add_options_page(
            __('Verificación de Identidad', 'identity-verification'),
            __('Verificación de Identidad', 'identity-verification'),
            'manage_options',
            'identity-verification-settings',
            array($this, 'settings_page_html')
        );
    }

    public function register_settings() {
        register_setting('iv_settings_group', 'iv_redirect_no_verificado');
        register_setting('iv_settings_group', 'iv_content_pendiente');
        register_setting('iv_settings_group', 'iv_redirect_verificado');
        register_setting('iv_settings_group', 'iv_content_verificado');

        add_settings_section(
            'iv_settings_section',
            __('Configuración de Contenidos de Verificación', 'identity-verification'),
            null,
            'identity-verification-settings'
        );

        add_settings_field(
            'iv_redirect_no_verificado',
            __('Página de Redirección (No Verificado)', 'identity-verification'),
            array($this, 'redirect_no_verificado_field'),
            'identity-verification-settings',
            'iv_settings_section'
        );

        add_settings_field(
            'iv_content_pendiente',
            __('Contenido (Pendiente)', 'identity-verification'),
            array($this, 'content_pendiente_field'),
            'identity-verification-settings',
            'iv_settings_section'
        );

        add_settings_field(
            'iv_redirect_verificado',
            __('Página de Redirección (Verificado)', 'identity-verification'),
            array($this, 'redirect_verificado_field'),
            'identity-verification-settings',
            'iv_settings_section'
        );

        add_settings_field(
            'iv_content_verificado',
            __('Contenido (Verificado)', 'identity-verification'),
            array($this, 'content_verificado_field'),
            'identity-verification-settings',
            'iv_settings_section'
        );
    }

    public function redirect_no_verificado_field() {
        $value = get_option('iv_redirect_no_verificado');
        wp_dropdown_pages(array(
            'name' => 'iv_redirect_no_verificado',
            'selected' => $value,
            'show_option_none' => __('Seleccionar página', 'identity-verification'),
            'option_none_value' => ''
        ));
        echo '<p>' . __('Seleccione la página donde se redirigirán los usuarios no verificados y asegúrese de insertar el shortcode [iv_verification_form] en esa página.', 'identity-verification') . '</p>';
    }

    public function content_pendiente_field() {
        $value = get_option('iv_content_pendiente', '');
        wp_editor($value, 'iv_content_pendiente', array(
            'textarea_name' => 'iv_content_pendiente',
        ));
    }

    public function redirect_verificado_field() {
        $value = get_option('iv_redirect_verificado');
        wp_dropdown_pages(array(
            'name' => 'iv_redirect_verificado',
            'selected' => $value,
            'show_option_none' => __('Seleccionar página', 'identity-verification'),
            'option_none_value' => ''
        ));
    }

    public function content_verificado_field() {
        $value = get_option('iv_content_verificado', '');
        wp_editor($value, 'iv_content_verificado', array(
            'textarea_name' => 'iv_content_verificado',
        ));
    }

    public function settings_page_html() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('iv_settings_group');
                do_settings_sections('identity-verification-settings');
                submit_button(__('Guardar cambios', 'identity-verification'));
                ?>
            </form>
        </div>
        <?php
    }
}
