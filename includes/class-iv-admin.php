<?php
if (!defined('ABSPATH')) {
    exit;
}

class IV_Admin {

    public function __construct() {
        // Add custom user profile fields to the user profile page
        add_action('show_user_profile', array($this, 'add_custom_user_profile_fields'));
        add_action('edit_user_profile', array($this, 'add_custom_user_profile_fields'));

        // Save custom user profile fields when the user profile is updated
        add_action('personal_options_update', array($this, 'save_custom_user_profile_fields'));
        add_action('edit_user_profile_update', array($this, 'save_custom_user_profile_fields'));

        // Hooks to add a verification status column in the users table
        add_filter('manage_users_columns', array($this, 'add_verification_status_column'));
        add_action('manage_users_custom_column', array($this, 'show_verification_status_column'), 10, 3);
        add_filter('manage_users_sortable_columns', array($this, 'make_verification_status_column_sortable'));

        // Hooks to add the verification status bulk change select
        add_action('restrict_manage_users', array($this, 'add_verification_status_bulk_change'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_iv_bulk_update_verification_status', array($this, 'bulk_update_verification_status'));
    }

    // Function to add custom user profile fields
    public function add_custom_user_profile_fields($user) {
        // Display the custom fields in a table
        ?>
        <h3><?php _e("Verificación de Identidad", "identity-verification"); ?></h3>
        <table class="form-table">
            <!-- Custom field for identity verification number -->
            <tr>
                <th><label for="identity_verification"><?php _e("Número de DNI", "identity-verification"); ?></label></th>
                <td>
                    <input type="text" name="identity_verification" id="identity_verification" value="<?php echo esc_attr(get_the_author_meta('identity_verification', $user->ID)); ?>" class="regular-text" readonly /><br />
                    <span class="description"><?php _e("Número de DNI proporcionado por el usuario.", "identity-verification"); ?></span>
                </td>
            </tr>
            <!-- Custom field for identity verification file -->
            <tr>
                <th><label for="identity_verification_file"><?php _e("Foto del DNI", "identity-verification"); ?></label></th>
                <td>
                    <?php
                    // Display a link to the uploaded file if available
                    $file_url = get_the_author_meta('identity_verification_file', $user->ID);
                    if ($file_url) {
                        echo '<a href="' . esc_url($file_url) . '" target="_blank">' . __('Ver archivo', 'identity-verification') . '</a>';
                    } else {
                        _e('No se ha subido ningún archivo.', 'identity-verification');
                    }
                    ?><br />
                    <span class="description"><?php _e("Foto del DNI subida por el usuario.", "identity-verification"); ?></span>
                </td>
            </tr>
            <!-- Custom field for verification status -->
            <tr>
                <th><label for="verification_status"><?php _e("Estado de Verificación", "identity-verification"); ?></label></th>
                <td>
                    <select name="verification_status" id="verification_status">
                        <!-- Options for different verification statuses -->
                        <option value="no_verificado" <?php selected(get_the_author_meta('verification_status', $user->ID), 'no_verificado'); ?>><?php _e("No Verificado", "identity-verification"); ?></option>
                        <option value="pendiente" <?php selected(get_the_author_meta('verification_status', $user->ID), 'pendiente'); ?>><?php _e("Pendiente", "identity-verification"); ?></option>
                        <option value="verificado" <?php selected(get_the_author_meta('verification_status', $user->ID), 'verificado'); ?>><?php _e("Verificado", "identity-verification"); ?></option>
                        <option value="fallo" <?php selected(get_the_author_meta('verification_status', $user->ID), 'fallo'); ?>><?php _e("Fallo en Verificación", "identity-verification"); ?></option>
                    </select><br />
                    <span class="description"><?php _e("Seleccione el estado de verificación.", "identity-verification"); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }

    // Function to save custom user profile fields
    public function save_custom_user_profile_fields($user_id) {
        // Check user permissions
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        // Save the identity verification and verification status fields
        if (isset($_POST['identity_verification'])) {
            update_user_meta($user_id, 'identity_verification', sanitize_text_field($_POST['identity_verification']));
        }

        if (isset($_POST['verification_status'])) {
            update_user_meta($user_id, 'verification_status', sanitize_text_field($_POST['verification_status']));
        }
    }

    // Function to add the verification status column in the users table
    public function add_verification_status_column($columns) {
        $columns['verification_status'] = __('Estado de Verificación', 'identity-verification');
        return $columns;
    }

    // Function to display the verification status column value
    public function show_verification_status_column($value, $column_name, $user_id) {
        if ($column_name == 'verification_status') {
            // Get the verification status for the user
            $status = get_user_meta($user_id, 'verification_status', true);
            if ($status === '') {
                $status = 'no_verificado';
            }
            return esc_html($status);
        }
        return $value;
    }

    // Function to make the verification status column sortable
    public function make_verification_status_column_sortable($columns) {
        $columns['verification_status'] = 'verification_status';
        return $columns;
    }

    // Function to add the verification status bulk change select
    public function add_verification_status_bulk_change($which) {
        if ('top' !== $which) {
            return;
        }
        ?>
        <div class="alignleft actions">
            <select name="bulk_verification_status" id="bulk_verification_status">
                <option value=""><?php _e('Cambiar verificación a...', 'identity-verification'); ?></option>
                <option value="no_verificado"><?php _e('No Verificado', 'identity-verification'); ?></option>
                <option value="pendiente"><?php _e('Pendiente', 'identity-verification'); ?></option>
                <option value="verificado"><?php _e('Verificado', 'identity-verification'); ?></option>
                <option value="fallo"><?php _e('Fallo en Verificación', 'identity-verification'); ?></option>
            </select>
            <button type="button" class="button" id="apply-verification-status"><?php _e('Aplicar', 'identity-verification'); ?></button>
        </div>
        <?php
    }

    // Function to enqueue admin scripts
    public function enqueue_admin_scripts($hook) {
        if ('users.php' !== $hook) {
            return;
        }

        // Enqueue the admin script and localize the script with necessary data
        wp_enqueue_script('iv-admin-script', plugin_dir_url(__FILE__) . 'js/iv-admin.js', array('jquery'), '1.0', true);
        wp_localize_script('iv-admin-script', 'ivAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('iv_nonce'),
            'status_missing' => __('Por favor, selecciona un estado de verificación.', 'identity-verification'),
            'select_users' => __('Por favor, selecciona al menos un usuario.', 'identity-verification')
        ));
    }

    // Function to handle bulk update of verification status
    public function bulk_update_verification_status() {
        // Check the AJAX referer and user permissions
        check_ajax_referer('iv_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('No tienes permiso para hacer esto.', 'identity-verification'));
        }

        // Get the user IDs and verification status from the AJAX request
        $user_ids = isset($_POST['user_ids']) ? array_map('absint', $_POST['user_ids']) : array();
        $status   = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

        // Update the verification status for each user
        if (empty($user_ids) || empty($status)) {
            wp_send_json_error(__('Faltan parámetros.', 'identity-verification'));
        }

        foreach ($user_ids as $user_id) {
            update_user_meta($user_id, 'verification_status', $status);
        }

        wp_send_json_success(__('Estado de verificación actualizado.', 'identity-verification'));
    }
}