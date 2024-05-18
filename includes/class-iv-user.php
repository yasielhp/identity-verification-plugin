<?php
if (!defined('ABSPATH')) {
    exit;
}

class IV_User {

    public function __construct() {
        add_shortcode('iv_verification_form', array($this, 'verification_form_shortcode'));
    }

    public function verification_form_shortcode() {
        // Verificar si el usuario ha iniciado sesión
        if (!is_user_logged_in()) {
            return '<p>' . esc_html(__('Debes iniciar sesión para acceder a esta página.', 'identity-verification')) . '</p>';
        }

        // Obtener el ID del usuario actual
        $user_id = get_current_user_id();

        // Obtener el estado de verificación del usuario
        $verification_status = get_user_meta($user_id, 'verification_status', true);

        // Manejar la actualización de la verificación
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_verification'])) {
            $this->handle_verification_submission($user_id);
            // Actualizar el estado de verificación después de la presentación
            $verification_status = 'pendiente';
        }

        // Verificar el estado de verificación y mostrar el contenido correspondiente
        if ($verification_status === 'verificado') {
            return wpautop(get_option('iv_content_verificado', ''));
        }

        if ($verification_status === 'pendiente') {
            return wpautop(get_option('iv_content_pendiente', ''));
        }

        // Mostrar el formulario si el estado no es 'pendiente' ni 'verificado'
        ob_start();
        include plugin_dir_path(__FILE__) . '../templates/verification-form.php';
        return ob_get_clean();
    }

    private function handle_verification_submission($user_id) {
        if (isset($_POST['identity_verification_info']) && isset($_FILES['identity_verification_file'])) {
            // Actualizar el meta de usuario con el número de DNI
            update_user_meta($user_id, 'identity_verification', sanitize_text_field($_POST['identity_verification_info']));

            // Manejo de la subida del archivo
            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }

            $uploadedfile = $_FILES['identity_verification_file'];
            $upload_overrides = array('test_form' => false);
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {
                // Actualizar el meta de usuario con la URL del archivo
                update_user_meta($user_id, 'identity_verification_file', $movefile['url']);
                // Actualizar el estado de verificación a 'pendiente'
                update_user_meta($user_id, 'verification_status', 'pendiente');
            } else {
                echo '<p>' . esc_html(__('Error al subir el archivo: ', 'identity-verification')) . esc_html($movefile['error']) . '</p>';
            }
        }
    }
}
