<form method="post">
    <p>
        <label for="identity_verification_info"><?php _e('Información de Verificación:', 'identity-verification'); ?></label>
        <input type="text" name="identity_verification_info" id="identity_verification_info" required>
    </p>
    <p>
        <input type="submit" name="submit_verification" value="<?php _e('Enviar', 'identity-verification'); ?>">
    </p>
</form>
<?php
if (isset($_POST['submit_verification'])) {
    $user_id = get_current_user_id();
    update_user_meta($user_id, 'identity_verification', sanitize_text_field($_POST['identity_verification_info']));
    update_user_meta($user_id, 'verification_status', 'verificado');
}
?>
