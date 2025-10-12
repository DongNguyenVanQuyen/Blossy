<?php
define('CLOUD_NAME', 'dgdes7cnj');
define('API_KEY', '316397258317316');
define('API_SECRET', 'JEXTgxtdyU53R0lJYLNzSGrRNDc');

function uploadToCloudinary($filePath, $folder = 'webbanhoa')
{
    $url = "https://api.cloudinary.com/v1_1/" . CLOUD_NAME . "/image/upload";
    $timestamp = time();
    $signature = sha1("folder={$folder}&timestamp={$timestamp}" . API_SECRET);

    $params = [
        'file' => new CURLFile($filePath),
        'api_key' => API_KEY,
        'timestamp' => $timestamp,
        'folder' => $folder,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['secure_url'] ?? null;
}
