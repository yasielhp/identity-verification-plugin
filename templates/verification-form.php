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
