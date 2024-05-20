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
            return '<p>' . esc_html(__('You must log in to access this page.', 'identity-verification')) . '</p>';
        }

        // Obtener el ID del usuario actual
        $user_id = get_current_user_id();

        // Obtener el estado de verificación del usuario
        $verification_status = get_user_meta($user_id, 'verification_status', true);

        // Manejar la actualización de la verificación
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_verification'])) {
            $this->handle_verification_submission($user_id);
            // Recargar el estado de verificación después de la presentación
            $verification_status = get_user_meta($user_id, 'verification_status', true);
        }

        // Verificar el estado de verificación y mostrar el contenido correspondiente
        if ($verification_status === 'verified') {
            return '
            <div class="box-status verified">
                <h3 class="box-status-title verified">' . esc_html__('Verified', 'identity-verification') . '</h3>
                <p class="box-status-description verified">' . esc_html__('Your identity has been successfully verified. Thank you for helping to maintain a safe environment.', 'identity-verification') . '</p>
                <a class="button" href="/">' . esc_html__('Access', 'identity-verification') . '</a>
            </div>';
        }

        if ($verification_status === 'pending') {
            return '
            <div class="box-status process">
                <h3 class="box-status-title process">' . esc_html__('In Process', 'identity-verification') . '</h3>
                <p class="box-status-description process">' . esc_html__('Your identity verification is in process. This process will be completed within 24 hours during business hours.', 'identity-verification') . '</p>
                <a class="button" href="/">' . esc_html__('Update', 'identity-verification') . '</a>
            </div>';
        }

        // Mostrar el formulario si el estado no es 'pending' ni 'verified'
        ob_start();
        include plugin_dir_path(__FILE__) . '../templates/verification-form.php';
        return ob_get_clean();
    }

    private function handle_verification_submission($user_id) {
        if (isset($_POST['identity_verification_info']) && isset($_FILES['identity_verification_file'])) {
            // Verificar tamaño del archivo
            if ($_FILES['identity_verification_file']['size'] > 2 * 1024 * 1024) {
                echo '<p>' . esc_html(__('The file size exceeds the 2MB limit.', 'identity-verification')) . '</p>';
                return;
            }

            // Validar y sanitizar el número de DNI
            $dni_number = sanitize_text_field($_POST['identity_verification_info']);
            if (empty($dni_number)) {
                echo '<p>' . esc_html(__('Invalid DNI number.', 'identity-verification')) . '</p>';
                return;
            }

            // Manejo de la subida del archivo
            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }

            $uploadedfile = $_FILES['identity_verification_file'];
            $upload_overrides = array('test_form' => false);
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {
                // Actualizar el meta de usuario con el número de DNI
                update_user_meta($user_id, 'identity_verification', $dni_number);
                // Actualizar el meta de usuario con la URL del archivo
                update_user_meta($user_id, 'identity_verification_file', esc_url_raw($movefile['url']));
                // Actualizar el estado de verificación a 'pending'
                update_user_meta($user_id, 'verification_status', 'pending');
            } else {
                echo '<p>' . esc_html(__('Error uploading file: ', 'identity-verification')) . esc_html($movefile['error']) . '</p>';
            }
        }
    }
}
?>
