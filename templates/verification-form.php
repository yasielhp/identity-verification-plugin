<div class="verification-form-container">
    <div class="verification-form-header">
        <h1 class="verification-form-title"><?php echo esc_html__('Identity Verification', 'identity-verification'); ?></h1>
        <p class="verification-form-description">
            <?php echo esc_html__('Please enter your national identification number and upload a photo of your ID.', 'identity-verification'); ?>
        </p>
    </div>
    <form class="verification-form" method="post" enctype="multipart/form-data">
        <div class="verification-form-group">
            <label for="identity_verification_info" class="verification-form-label"><?php echo esc_html__('DNI Number', 'identity-verification'); ?></label>
            <input type="text" name="identity_verification_info" id="identity_verification_info" class="verification-form-input" placeholder="<?php echo esc_attr__('Enter your DNI number', 'identity-verification'); ?>" required>
        </div>
        <div class="verification-form-group">
            <label for="identity_verification_file" class="verification-form-label"><?php echo esc_html__('Photo of ID', 'identity-verification'); ?></label>
            <input type="file" name="identity_verification_file" id="identity_verification_file" class="verification-form-input" accept="image/*" required>
        </div>
        <button type="submit" name="submit_verification" class="verification-form-button"><?php echo esc_html__('Submit', 'identity-verification'); ?></button>
    </form>
</div>
