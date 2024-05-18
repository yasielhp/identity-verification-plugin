<?php
if (!defined('ABSPATH')) {
    exit;
}

class IV_Admin {

    public function __construct() {
        add_action('show_user_profile', array($this, 'add_custom_user_profile_fields'));
        add_action('edit_user_profile', array($this, 'add_custom_user_profile_fields'));

        add_action('personal_options_update', array($this, 'save_custom_user_profile_fields'));
        add_action('edit_user_profile_update', array($this, 'save_custom_user_profile_fields'));

        add_filter('manage_users_columns', array($this, 'add_verification_status_column'));
        add_action('manage_users_custom_column', array($this, 'show_verification_status_column'), 10, 3);
        add_filter('manage_users_sortable_columns', array($this, 'make_verification_status_column_sortable'));

        add_action('restrict_manage_users', array($this, 'add_verification_status_bulk_change'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_iv_bulk_update_verification_status', array($this, 'bulk_update_verification_status'));
    }

    public function add_custom_user_profile_fields($user) {
        ?>
        <h3><?php _e("Identity Verification", "identity-verification"); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="identity_verification"><?php _e("DNI Number", "identity-verification"); ?></label></th>
                <td>
                    <input type="text" name="identity_verification" id="identity_verification" value="<?php echo esc_attr(get_the_author_meta('identity_verification', $user->ID)); ?>" class="regular-text" readonly /><br />
                    <span class="description"><?php _e("DNI number provided by the user.", "identity-verification"); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="identity_verification_file"><?php _e("Photo of DNI", "identity-verification"); ?></label></th>
                <td>
                    <?php
                    $file_url = get_the_author_meta('identity_verification_file', $user->ID);
                    if ($file_url) {
                        echo '<a href="' . esc_url($file_url) . '" target="_blank">' . __('View file', 'identity-verification') . '</a>';
                    } else {
                        _e('No file uploaded.', 'identity-verification');
                    }
                    ?><br />
                    <span class="description"><?php _e("Photo of DNI uploaded by the user.", "identity-verification"); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="verification_status"><?php _e("Verification Status", "identity-verification"); ?></label></th>
                <td>
                    <select name="verification_status" id="verification_status">
                        <option value="not_verified" <?php selected(get_the_author_meta('verification_status', $user->ID), 'not_verified'); ?>><?php _e("Not Verified", "identity-verification"); ?></option>
                        <option value="pending" <?php selected(get_the_author_meta('verification_status', $user->ID), 'pending'); ?>><?php _e("Pending", "identity-verification"); ?></option>
                        <option value="verified" <?php selected(get_the_author_meta('verification_status', $user->ID), 'verified'); ?>><?php _e("Verified", "identity-verification"); ?></option>
                    </select><br />
                    <span class="description"><?php _e("Select the verification status.", "identity-verification"); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }

    public function save_custom_user_profile_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        if (isset($_POST['identity_verification'])) {
            update_user_meta($user_id, 'identity_verification', sanitize_text_field($_POST['identity_verification']));
        }

        if (isset($_POST['verification_status'])) {
            update_user_meta($user_id, 'verification_status', sanitize_text_field($_POST['verification_status']));
        }
    }

    public function add_verification_status_column($columns) {
        $columns['verification_status'] = __('Verification Status', 'identity-verification');
        return $columns;
    }

    public function show_verification_status_column($value, $column_name, $user_id) {
        if ($column_name == 'verification_status') {
            $status = get_user_meta($user_id, 'verification_status', true);
            return esc_html($status ? $status : 'not_verified');
        }
        return $value;
    }

    public function make_verification_status_column_sortable($columns) {
        $columns['verification_status'] = 'verification_status';
        return $columns;
    }

    public function add_verification_status_bulk_change($which) {
        if ('top' !== $which) {
            return;
        }
        ?>
        <div class="alignleft actions">
            <select name="bulk_verification_status" id="bulk_verification_status">
                <option value=""><?php _e('Change verification to...', 'identity-verification'); ?></option>
                <option value="not_verified"><?php _e('Not Verified', 'identity-verification'); ?></option>
                <option value="pending"><?php _e('Pending', 'identity-verification'); ?></option>
                <option value="verified"><?php _e('Verified', 'identity-verification'); ?></option>
            </select>
            <button type="button" class="button" id="apply-verification-status"><?php _e('Apply', 'identity-verification'); ?></button>
        </div>
        <?php
    }

    public function enqueue_admin_scripts($hook) {
        if ('users.php' !== $hook) {
            return;
        }

        wp_enqueue_script('iv-admin-script', plugin_dir_url(__FILE__) . 'js/iv-admin.js', array('jquery'), '1.0', true);
        wp_localize_script('iv-admin-script', 'ivAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('iv_nonce'),
            'status_missing' => __('Please select a verification status.', 'identity-verification'),
            'select_users' => __('Please select at least one user.', 'identity-verification')
        ));
    }

    public function bulk_update_verification_status() {
        check_ajax_referer('iv_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have permission to do this.', 'identity-verification'));
        }

        $user_ids = isset($_POST['user_ids']) ? array_map('absint', $_POST['user_ids']) : array();
        $status   = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

        if (empty($user_ids) || empty($status)) {
            wp_send_json_error(__('Missing parameters.', 'identity-verification'));
        }

        foreach ($user_ids as $user_id) {
            update_user_meta($user_id, 'verification_status', $status);
        }

        wp_send_json_success(__('Verification status updated.', 'identity-verification'));
    }
}
?>
