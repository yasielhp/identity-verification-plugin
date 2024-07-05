<?php
if (!defined('ABSPATH')) {
    exit;
}

// Asegúrate de que la ruta a autoload.php es correcta
if (file_exists(plugin_dir_path(__FILE__) . 'google-api-php-client/vendor/autoload.php')) {
    require_once plugin_dir_path(__FILE__) . 'google-api-php-client/vendor/autoload.php';
} elseif (file_exists(plugin_dir_path(__FILE__) . '../vendor/autoload.php')) {
    require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';
} else {
    exit('Could not find autoload.php. Please install the Google API client library.');
}

class IV_Google_Drive {

    private $client;
    private $service;
    private $folder_id = '1suwC1RAkMKwT_dqUEdwY74OQUAz05yzG';

    public function __construct() {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(plugin_dir_path(__FILE__) . 'service-account-credentials.json');
        $this->client->addScope(Google_Service_Drive::DRIVE);

        $this->service = new Google_Service_Drive($this->client);
    }

    public function upload_file($file, $custom_name) {
        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => $custom_name,
            'parents' => array($this->folder_id)
        ));
        $content = file_get_contents($file['tmp_name']);

        try {
            $driveFile = $this->service->files->create($fileMetadata, array(
                'data' => $content,
                'mimeType' => mime_content_type($file['tmp_name']),
                'uploadType' => 'multipart',
                'fields' => 'id, webViewLink'
            ));
            return array('success' => true, 'file_url' => $driveFile->webViewLink);
        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }
}
?>
