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
            __('Identity Verification Settings', 'identity-verification'),
            __('Identity Verification', 'identity-verification'),
            'manage_options',
            'identity-verification-settings',
            array($this, 'settings_page_html')
        );
    }

    public function register_settings() {
        register_setting('iv_settings_group', 'iv_redirect_no_verificado');

        add_settings_section(
            'iv_settings_section',
            __('Identity Verification Settings', 'identity-verification'),
            null,
            'identity-verification-settings'
        );

        add_settings_field(
            'iv_redirect_no_verificado',
            __('Redirection Page (Not Verified)', 'identity-verification'),
            array($this, 'redirect_no_verificado_field'),
            'identity-verification-settings',
            'iv_settings_section'
        );
    }

    public function redirect_no_verificado_field() {
        $value = get_option('iv_redirect_no_verificado');
        wp_dropdown_pages(array(
            'name' => 'iv_redirect_no_verificado',
            'selected' => $value,
            'show_option_none' => __('Select page', 'identity-verification'),
            'option_none_value' => ''
        ));
        echo '<p>' . __('Select the page where non-verified users will be redirected and ensure to insert the shortcode [iv_verification_form] on that page.', 'identity-verification') . '</p>';
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
                submit_button(__('Save changes', 'identity-verification'));
                ?>
            </form>
        </div>
        <?php
    }
}
?>
