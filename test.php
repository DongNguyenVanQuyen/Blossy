<?php
define('CLOUD_NAME', 'dgdes7cnj');
define('API_KEY', '316397258317316');
define('API_SECRET', 'JEXTgxtdyU53R0lJYLNzSGrRNDc');

function uploadToCloudinary($filePath, $folder = 'webbanhoa_test')
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

    if (curl_errno($ch)) {
        echo "❌ CURL Error: " . curl_error($ch);
        curl_close($ch);
        return null;
    }

    curl_close($ch);
    $result = json_decode($response, true);
    echo "<pre>Cloudinary Response:\n";
    print_r($result);
    echo "</pre>";

    return $result['secure_url'] ?? null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $tmp = $_FILES['photo']['tmp_name'];
        echo "<p>Đang upload lên Cloudinary...</p>";
        $url = uploadToCloudinary($tmp);
        if ($url) {
            echo "<p>✅ Upload thành công!</p>";
            echo "<img src='$url' style='max-width:300px'>";
        } else {
            echo "<p>❌ Upload thất bại!</p>";
        }
    } else {
        echo "<p>❌ PHP không nhận được file</p>";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
  <h2>TEST 2: Upload lên Cloudinary</h2>
  <input type="file" name="photo" accept="image/*">
  <br><br>
  <button type="submit">Upload</button>
</form>
