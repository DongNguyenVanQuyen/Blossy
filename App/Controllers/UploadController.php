<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Includes/Cloudinary_config.php';

class UploadController extends BaseController
{
    public function main()
    {
        header('Content-Type: application/json');

        if (!empty($_FILES['file']['tmp_name'])) {
            $url = uploadToCloudinary($_FILES['file']['tmp_name'], 'webbanhoa');
            if ($url) {
                echo json_encode(['success' => true, 'url' => $url]);
                return;
            }
        }
        echo json_encode(['success' => false]);
    }
}
