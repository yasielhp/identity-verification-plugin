<?php
if (!defined('ABSPATH')) {
    exit;
}

// Asegúrate de que la ruta a class-iv-google-drive.php es correcta
require_once plugin_dir_path(__FILE__) . 'class-iv-google-drive.php';

class IV_User {

    public function __construct() {
        add_shortcode('iv_verification_form', array($this, 'verification_form_shortcode'));
    }

    public function verification_form_shortcode() {
        if (!is_user_logged_in()) {
            return '<p>' . esc_html(__('You must log in to access this page.', 'identity-verification')) . '</p>';
        }

        $user_id = get_current_user_id();
        $verification_status = get_user_meta($user_id, 'verification_status', true);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_verification'])) {
            $this->handle_verification_submission($user_id);

            // Redirigir después de la presentación del formulario para evitar reenvío al actualizar
            wp_redirect(add_query_arg('verification_status', 'submitted', wp_get_referer()));
            exit;
        }

        // Mostrar mensajes de estado basados en la redirección
        if (isset($_GET['verification_status']) && $_GET['verification_status'] === 'submitted') {
            $verification_status = get_user_meta($user_id, 'verification_status', true);
        }

        if ($verification_status === 'verified') {
            return $this->get_verified_content();
        }

        if ($verification_status === 'pending') {
            return $this->get_pending_content();
        }

        ob_start();
        include plugin_dir_path(__FILE__) . '../templates/verification-form.php';
        return ob_get_clean();
    }

    private function get_verified_content() {
        return '
            <div class="box-status verified">
                <h3 class="box-status-title verified">' . esc_html__('Verified', 'identity-verification') . '</h3>
                <p class="box-status-description verified">' . esc_html__('Your identity has been successfully verified.', 'identity-verification') . '</p>
                <a class="button" href="/">' . esc_html__('Access', 'identity-verification') . '</a>
            </div>';
    }

    private function get_pending_content() {
        return '
            <div class="box-status process">
                <h3 class="box-status-title process">' . esc_html__('In Process', 'identity-verification') . '</h3>
                <p class="box-status-description process">' . esc_html__('Your identity verification is in process.', 'identity-verification') . '</p>
                <a class="button" href="/">' . esc_html__('Update', 'identity-verification') . '</a>
            </div>';
    }

    private function handle_verification_submission($user_id) {
        if (isset($_POST['identity_verification_info']) && isset($_FILES['identity_verification_file'])) {
            if ($_FILES['identity_verification_file']['size'] > 2 * 1024 * 1024) {
                echo '<p>' . esc_html(__('The file size exceeds the 2MB limit.', 'identity-verification')) . '</p>';
                return;
            }

            $dni_number = sanitize_text_field($_POST['identity_verification_info']);
            if (empty($dni_number)) {
                echo '<p>' . esc_html(__('Invalid DNI number.', 'identity-verification')) . '</p>';
                return;
            }

            // Utilizar el DNI número como el nombre del archivo personalizado
            $custom_file_name = sanitize_file_name($dni_number) . '.' . pathinfo($_FILES['identity_verification_file']['name'], PATHINFO_EXTENSION);

            $google_drive = new IV_Google_Drive();
            $upload_result = $google_drive->upload_file($_FILES['identity_verification_file'], $custom_file_name);
            if ($upload_result['success']) {
                update_user_meta($user_id, 'identity_verification', $dni_number);
                update_user_meta($user_id, 'identity_verification_file', esc_url_raw($upload_result['file_url']));
                update_user_meta($user_id, 'verification_status', 'pending');
            } else {
                echo '<p>' . esc_html(__('Error uploading file: ', 'identity-verification')) . esc_html($upload_result['error']) . '</p>';
            }
        }
    }
}
?>
