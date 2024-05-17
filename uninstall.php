<?php
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Eliminar opciones del plugin
delete_option('iv_redirect_no_verificado');
delete_option('iv_content_pendiente');
delete_option('iv_redirect_verificado');
delete_option('iv_content_verificado');
