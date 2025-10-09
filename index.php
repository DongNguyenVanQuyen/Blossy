<?php
// ============================================================
// 🔹 Khởi động session & output buffer
// ============================================================
session_start();
ob_start();

// ============================================================
// 🔹 Cấu hình ban đầu
// ============================================================
require_once 'App/Includes/config.php';

// ============================================================
// 🔹 Xử lý nhanh cho AJAX (Cart / Payment / Voucher / v.v.)
// ============================================================

// --- CartController (add / update / remove / clear)
if (
    isset($_GET['controller']) && $_GET['controller'] === 'cart' &&
    isset($_GET['action']) && in_array($_GET['action'], ['add', 'update', 'remove', 'clear'])
) {
    require_once __DIR__ . "/App/Controllers/CartController.php";
    $controller = new CartController();
    $controller->{$_GET['action']}();
    exit;
}

// --- VoucherController (apply)
if (
    isset($_GET['controller']) && $_GET['controller'] === 'voucher' &&
    in_array($_GET['action'], ['apply'])
) {
    require_once __DIR__ . "/App/Controllers/VoucherController.php";
    $controller = new VoucherController();
    $controller->{$_GET['action']}();
    exit;
}

// --- PaymentController (getMethods / add)
if (
    isset($_GET['controller']) && $_GET['controller'] === 'payment' &&
    in_array($_GET['action'], ['getMethods', 'add'])
) {
    require_once __DIR__ . "/App/Controllers/PaymentController.php";
    $controller = new PaymentController();
    $controller->{$_GET['action']}();
    exit;
}

// ============================================================
// 🔹 Lấy controller & action hiện tại
// ============================================================
$controller = isset($_GET['controller']) ? strtolower($_GET['controller']) : 'home';
$action     = isset($_GET['action']) ? strtolower($_GET['action']) : 'index';

// ============================================================
// ⚠️ Không xóa session last_order ngay sau khi đặt hàng
//    Chỉ xóa khi người dùng rời trang OrderCompleted
// ============================================================
if (
    isset($_SESSION['last_order']) &&
    !($controller === 'order' && in_array($action, ['showcompleted', 'complete']))
) {
    unset($_SESSION['last_order']);
}

// ============================================================
// 🔹 Gọi đúng Controller & Action
// ============================================================
$controllerName = ucfirst($controller) . 'Controller';
$actionName     = $action;
$controllerPath = __DIR__ . '/App/Controllers/' . $controllerName . '.php';

if (file_exists($controllerPath)) {
    require_once $controllerPath;

    if (class_exists($controllerName)) {
        $controllerObject = new $controllerName();

        // ✅ Kiểm tra xem action có tồn tại
        if (method_exists($controllerObject, $actionName)) {
            // 🧩 Tự động truyền tham số nếu có trong URL
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $controllerObject->$actionName($_GET['id']); // Gọi action có id
            } else {
                $controllerObject->$actionName(); // Gọi action bình thường
            }
        } else {
            echo "<h3>❌ Không tìm thấy action <b>$actionName</b> trong controller <b>$controllerName</b>.</h3>";
        }
    } else {
        echo "<h3>❌ Không tìm thấy class controller: <b>$controllerName</b>.</h3>";
    }
} else {
    echo "<h3>❌ Không tìm thấy file controller: <b>$controllerPath</b>.</h3>";
}

// ============================================================
// 🔹 Kết thúc buffer
// ============================================================
ob_end_flush();
