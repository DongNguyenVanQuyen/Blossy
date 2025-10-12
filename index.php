<?php
session_start();
ob_start();
require_once 'App/Includes/config.php';

// ============================================================
// 🔹 Xác định controller & action NGAY TỪ ĐẦU
// ============================================================
$controller = isset($_GET['controller']) ? strtolower($_GET['controller']) : 'home';
$action     = isset($_GET['action']) ? strtolower($_GET['action']) : 'index';

// ============================================================
// 🔹 Xử lý nhanh cho AJAX (Cart / Payment / Voucher / v.v.)
// ============================================================

// --- CartController (add / update / remove / clear)
if ($controller === 'cart' && in_array($action, ['add', 'update', 'remove', 'clear'])) {
    require_once __DIR__ . "/App/Controllers/CartController.php";
    $controllerObj = new CartController();
    $controllerObj->$action();
    exit;
}

// --- VoucherController (apply)
if ($controller === 'voucher' && $action === 'apply') {
    require_once __DIR__ . "/App/Controllers/VoucherController.php";
    $controllerObj = new VoucherController();
    $controllerObj->$action();
    exit;
}

// --- PaymentController (getMethods / add)
if ($controller === 'payment' && in_array($action, ['getmethods', 'add'])) {
    require_once __DIR__ . "/App/Controllers/PaymentController.php";
    $controllerObj = new PaymentController();
    $controllerObj->$action();
    exit;
}

// ============================================================
// 🔹 Xử lý controller admin (AdminProductController...)
// ============================================================
if ($controller === 'adminproduct') {
    require_once __DIR__ . '/App/Controllers/AdminProductController.php';
    $controllerObject = new AdminProductController();
    if (method_exists($controllerObject, $action)) {
        isset($_GET['id']) ? $controllerObject->$action($_GET['id']) : $controllerObject->$action();
    } else {
        echo "<h3>❌ Action không tồn tại: $action</h3>";
    }
    exit;
}


// ============================================================
// ⚠️ Không xóa session last_order ngay sau khi đặt hàng
// ============================================================
if (
    isset($_SESSION['last_order']) &&
    !($controller === 'order' && in_array($action, ['showcompleted', 'complete']))
) {
    unset($_SESSION['last_order']);
}

// Nếu controller = admin mà không có action => tự động về dashboard
if ($controller === 'admin' && empty($_GET['action'])) {
    $action = 'dashboard';
}

// ============================================================
// 🔹 UploadController (upload ảnh Cloudinary)
// ============================================================
if ($controller === 'upload') {
    require_once __DIR__ . '/App/Controllers/UploadController.php';
    $controllerObj = new UploadController();
    if (method_exists($controllerObj, $action)) {
        $controllerObj->$action();
    } else {
        echo json_encode(['success' => false, 'message' => "❌ Không tìm thấy action $action"]);
    }
    exit;
}

// ============================================================
// 🔹 Gọi đúng Controller & Action
// ============================================================
$controllerName = ucfirst($controller) . 'Controller';
$controllerPath = __DIR__ . '/App/Controllers/' . $controllerName . '.php';

if (file_exists($controllerPath)) {
    require_once $controllerPath;
    if (class_exists($controllerName)) {
        $controllerObj = new $controllerName();
        if (method_exists($controllerObj, $action)) {
            isset($_GET['id']) ? $controllerObj->$action($_GET['id']) : $controllerObj->$action();
        } else {
            echo "<h3>❌ Không tìm thấy action <b>$action</b> trong controller <b>$controllerName</b>.</h3>";
        }
    } else {
        echo "<h3>❌ Không tìm thấy class controller: <b>$controllerName</b>.</h3>";
    }
} else {
    echo "<h3>❌ Không tìm thấy file controller: <b>$controllerPath</b>.</h3>";
}

ob_end_flush();
