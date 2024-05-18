<?php
if (!defined('ABSPATH')) {
    exit;
}

// Funciones comunes del plugin

// Ejemplo de función común
function iv_get_user_verification_status($user_id) {
    return get_user_meta($user_id, 'verification_status', true);
}
