<div class="verification-form-container">
    <div class="verification-form-header">
        <h1 class="verification-form-title"><?php _e('Identity Verification', 'identity-verification'); ?></h1>
        <p class="verification-form-description">
            <?php _e('Please enter your national identification number and upload a photo of your ID.', 'identity-verification'); ?>
        </p>
    </div>
    <form class="verification-form" method="post" enctype="multipart/form-data">
        <div class="verification-form-group">
            <label for="identity_verification_info" class="verification-form-label"><?php _e('DNI Number', 'identity-verification'); ?></label>
            <input type="text" name="identity_verification_info" id="identity_verification_info" class="verification-form-input" placeholder="<?php _e('Enter your DNI number', 'identity-verification'); ?>" required>
        </div>
        <div class="verification-form-group">
            <label for="identity_verification_file" class="verification-form-label"><?php _e('Photo of ID', 'identity-verification'); ?></label>
            <input type="file" name="identity_verification_file" id="identity_verification_file" class="verification-form-input" accept="image/*" required>
        </div>
        <button type="submit" name="submit_verification" class="verification-form-button"><?php _e('Submit', 'identity-verification'); ?></button>
    </form>
</div>
