<?php
// Cấu hình ban đầu
require_once 'App/Includes/config.php';
require_once 'App/Includes/head.php';
require_once 'App/Includes/Script.php';

// Lấy controller và action từ URL
$controllerName = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : 'HomeController';
$actionName = isset($_GET['action']) ? $_GET['action'] : 'index';

// Tạo đường dẫn tới controller
$controllerPath = __DIR__ . '/App/Controllers/' . $controllerName . '.php';


if (file_exists($controllerPath)) {
    require_once $controllerPath;

    if (class_exists($controllerName)) {
        $controllerObject = new $controllerName();

        if (method_exists($controllerObject, $actionName)) {
            $controllerObject->$actionName();
        } else {
            echo "❌ Không tìm thấy action: $actionName trong $controllerName";
        }
    } else {
        echo "❌ Không tìm thấy class: $controllerName";
    }
} else {
    echo "❌ Không tìm thấy controller: $controllerPath";
}
