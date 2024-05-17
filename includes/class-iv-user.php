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
            return '<p>' . __('Debes iniciar sesión para acceder a esta página.', 'identity-verification') . '</p>';
        }

        // Obtener el ID del usuario actual
        $user_id = get_current_user_id();

        // Obtener el estado de verificación del usuario
        $verification_status = get_user_meta($user_id, 'verification_status', true);

        // Obtener la URL actual
        global $wp;
        $current_url = home_url(add_query_arg(array(), $wp->request));

        // Verificar el estado de verificación y mostrar el contenido correspondiente
        if ($verification_status === 'verificado') {
            return wpautop(get_option('iv_content_verificado', ''));
        }

        if ($verification_status === 'pendiente') {
            return wpautop(get_option('iv_content_pendiente', ''));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_verification'])) {
            // Manejar la actualización de la verificación
            update_user_meta($user_id, 'identity_verification', sanitize_text_field($_POST['identity_verification_info']));

            // Manejo de la subida del archivo
            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }

            $uploadedfile = $_FILES['identity_verification_file'];
            $upload_overrides = array('test_form' => false);
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {
                update_user_meta($user_id, 'identity_verification_file', $movefile['url']);
                update_user_meta($user_id, 'verification_status', 'pendiente');

                // Redirigir después de la verificación
                $redirect_url = get_option('iv_redirect_pendiente', home_url());

                if ($current_url !== $redirect_url && !headers_sent()) {
                    wp_redirect($redirect_url);
                    exit;
                }
            } else {
                return '<p>' . __('Error al subir el archivo: ', 'identity-verification') . $movefile['error'] . '</p>';
            }
        }

        ob_start();
        ?>
        <form method="post" enctype="multipart/form-data">
            <p>
                <label for="identity_verification_info"><?php _e('Número de DNI:', 'identity-verification'); ?></label>
                <input type="text" name="identity_verification_info" id="identity_verification_info" required>
            </p>
            <p>
                <label for="identity_verification_file"><?php _e('Subir foto del DNI:', 'identity-verification'); ?></label>
                <input type="file" name="identity_verification_file" id="identity_verification_file" accept="image/*" required>
            </p>
            <p>
                <input type="submit" name="submit_verification" value="<?php _e('Enviar', 'identity-verification'); ?>">
            </p>
        </form>
        <?php
        return ob_get_clean();
    }
}
