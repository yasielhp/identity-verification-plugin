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
        // Añade una página de opciones en el menú de administración
        add_options_page(
            __('Verificación de Identidad', 'identity-verification'), // Título de la página
            __('Verificación de Identidad', 'identity-verification'), // Título del menú
            'manage_options', // Capacidad requerida para acceder a la página
            'identity-verification-settings', // Slug de la página
            array($this, 'settings_page_html') // Función que muestra el contenido de la página
        );
    }

    public function register_settings() {
        // Registra las opciones de configuración
        register_setting('iv_settings_group', 'iv_redirect_no_verificado');
        register_setting('iv_settings_group', 'iv_content_pendiente');
        register_setting('iv_settings_group', 'iv_redirect_verificado');
        register_setting('iv_settings_group', 'iv_content_fallo');
        register_setting('iv_settings_group', 'iv_content_verificado'); // Nueva opción para contenido verificado

        // Añade una sección de configuración
        add_settings_section(
            'iv_settings_section', // ID de la sección
            __('Configuración de Contenidos de Verificación', 'identity-verification'), // Título de la sección
            null, // Callback para mostrar el contenido de la sección (no se utiliza en este caso)
            'identity-verification-settings' // Slug de la página en la que se muestra la sección
        );

        // Añade un campo de configuración para la página de redirección (No Verificado)
        add_settings_field(
            'iv_redirect_no_verificado', // ID del campo
            __('Página de Redirección (No Verificado)', 'identity-verification'), // Título del campo
            array($this, 'redirect_no_verificado_field'), // Callback para mostrar el campo
            'identity-verification-settings', // Slug de la página en la que se muestra el campo
            'iv_settings_section' // ID de la sección a la que pertenece el campo
        );

        // Añade un campo de configuración para el contenido (Pendiente)
        add_settings_field(
            'iv_content_pendiente', // ID del campo
            __('Contenido (Pendiente)', 'identity-verification'), // Título del campo
            array($this, 'content_pendiente_field'), // Callback para mostrar el campo
            'identity-verification-settings', // Slug de la página en la que se muestra el campo
            'iv_settings_section' // ID de la sección a la que pertenece el campo
        );

        // Añade un campo de configuración para la página de redirección (Verificado)
        add_settings_field(
            'iv_redirect_verificado', // ID del campo
            __('Página de Redirección (Verificado)', 'identity-verification'), // Título del campo
            array($this, 'redirect_verificado_field'), // Callback para mostrar el campo
            'identity-verification-settings', // Slug de la página en la que se muestra el campo
            'iv_settings_section' // ID de la sección a la que pertenece el campo
        );

        // Añade un campo de configuración para el contenido (Fallo en Verificación)
        add_settings_field(
            'iv_content_fallo', // ID del campo
            __('Contenido (Fallo en Verificación)', 'identity-verification'), // Título del campo
            array($this, 'content_fallo_field'), // Callback para mostrar el campo
            'identity-verification-settings', // Slug de la página en la que se muestra el campo
            'iv_settings_section' // ID de la sección a la que pertenece el campo
        );

        // Añade un campo de configuración para el contenido (Verificado)
        add_settings_field(
            'iv_content_verificado', // ID del campo
            __('Contenido (Verificado)', 'identity-verification'), // Título del campo
            array($this, 'content_verificado_field'), // Callback para mostrar el campo
            'identity-verification-settings', // Slug de la página en la que se muestra el campo
            'iv_settings_section' // ID de la sección a la que pertenece el campo
        );
    }

    public function redirect_no_verificado_field() {
        // Muestra un campo de selección de página para la página de redirección (No Verificado)
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
        // Muestra un editor de texto enriquecido para el contenido (Pendiente)
        $value = get_option('iv_content_pendiente', '');
        wp_editor($value, 'iv_content_pendiente', array(
            'textarea_name' => 'iv_content_pendiente',
        ));
    }

    public function redirect_verificado_field() {
        // Muestra un campo de selección de página para la página de redirección (Verificado)
        $value = get_option('iv_redirect_verificado');
        wp_dropdown_pages(array(
            'name' => 'iv_redirect_verificado',
            'selected' => $value,
            'show_option_none' => __('Seleccionar página', 'identity-verification'),
            'option_none_value' => ''
        ));
    }

    public function content_fallo_field() {
        // Muestra un editor de texto enriquecido para el contenido (Fallo en Verificación)
        $value = get_option('iv_content_fallo', '');
        wp_editor($value, 'iv_content_fallo', array(
            'textarea_name' => 'iv_content_fallo',
        ));
    }

    public function content_verificado_field() {
        // Muestra un editor de texto enriquecido para el contenido (Verificado)
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
                settings_fields('iv_settings_group'); // Muestra los campos ocultos necesarios para la seguridad
                do_settings_sections('identity-verification-settings'); // Muestra las secciones y campos de configuración
                submit_button(__('Guardar cambios', 'identity-verification')); // Muestra el botón de guardar cambios
                ?>
            </form>
        </div>
        <?php
    }
}
