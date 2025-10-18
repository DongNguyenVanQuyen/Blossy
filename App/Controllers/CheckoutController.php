<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Models/CartModel.php';
require_once __DIR__ . '/../Models/PaymentModel.php';
require_once __DIR__ . '/../Models/VoucherModel.php';

class CheckoutController extends BaseController
{
    public function buyNow()
{
    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_SESSION['user']['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để mua hàng.']);
        exit;
    }

    $productId = $_POST['product_id'] ?? null;
    $quantity  = (int)($_POST['quantity'] ?? 1);

    if (!$productId || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
        exit;
    }

    // ✅ Lấy thông tin sản phẩm từ DB
    require_once __DIR__ . '/../Models/ProductModel.php';
    $productModel = new ProductModel();
    $product = $productModel->getById($productId);

    if (!$product || !$product['is_active']) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.']);
        exit;
    }

    // ✅ Nếu vượt quá tồn kho
    if ($quantity > ($product['stock'] ?? 0)) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không đủ hàng.']);
        exit;
    }

    $_SESSION['buy_now'] = [
        [
            'product_id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'old_price' => $product['compare_at_price'] ?? $product['price'],
            'quantity' => $quantity,
            'image_url' => $product['url'] ?? '',
            'stock' => $product['stock']
        ]
    ];


    echo json_encode(['success' => true]);
    exit;
}public function buyinCard()
{
    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_SESSION['user']['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để mua hàng.']);
        exit;
    }

    // ✅ Đọc dữ liệu JSON từ fetch body
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input['products'])) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
        exit;
    }

    require_once __DIR__ . '/../Models/ProductModel.php';
    $productModel = new ProductModel();

    $buyNowItems = [];
    foreach ($input['products'] as $p) {
        $productId = (int)($p['product_id'] ?? 0);
        $quantity  = max(1, (int)($p['quantity'] ?? 1));

        $product = $productModel->getById($productId);

        if (!$product || !$product['is_active']) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.']);
            exit;
        }

        if ($quantity > ($product['stock'] ?? 0)) {
            echo json_encode(['success' => false, 'message' => "Sản phẩm '{$product['name']}' không đủ hàng."]);
            exit;
        }

        $buyNowItems[] = [
            'product_id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image_url' => $product['url'] ?? '',
            'stock' => $product['stock']
        ];
    }

    // ✅ Lưu vào session để trang Checkout hiển thị
    $_SESSION['buy_now'] = $buyNowItems;

    echo json_encode([
        'success' => true,
        'message' => 'Tạo đơn hàng tạm thành công.'
    ]);
    exit;
}


  public function index()
{
    global $title;
    $title = "Thanh Toán | Blossy";

    $userId = $_SESSION['user']['user_id'] ?? null;
    if (!$userId) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Bạn cần đăng nhập để thanh toán!'
        ];
        header("Location: " . BASE_URL . "index.php?controller=auth&action=login");
        exit;
    }


    $userModel = new UserModel();
    $user = $userModel->getUserById($userId);
    $user_address = $userModel->getAddress($userId);

    $cartModel = new CartModel();

    // ✅ Ưu tiên "Mua ngay"
    if (!empty($_SESSION['buy_now'])) {
        $cartItems = $_SESSION['buy_now'];
    } else {
        $cartItems = $cartModel->getCartItemsByUser($userId);
    }

    if (!is_array($cartItems)) $cartItems = [];

    // ✅ Tính tổng
    $subtotal = 0;
    foreach ($cartItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    // ✅ Chỉ xử lý voucher người dùng nhập (không auto promotion)
    $voucherCode = $_GET['voucher'] ?? '';
    $discount = 0;
    $voucher = null;

    if ($voucherCode !== '') {
        $voucherModel = new VoucherModel();
        $voucher = $voucherModel->getActiveVoucher($voucherCode);

        if ($voucher && $subtotal >= ($voucher['min_order_total'] ?? 0)) {
            $discount = ($voucher['type'] === 'percent')
                ? min($subtotal * ($voucher['value'] / 100), $voucher['max_discount'] ?? $subtotal)
                : $voucher['value'];
        } else {
            $voucher = null;
        }
    }

    $shippingFee = 30000;
    $total = $subtotal - $discount + $shippingFee;
    

    // ✅ Lấy phương thức thanh toán
    $paymentModel = new PaymentModel();
    $methods = $paymentModel->getUserMethods($userId);

    $data = [
        'user' => $user,
        'user_address' => $user_address,
        'cartItems' => $cartItems,
        'methods' => $methods,
        'voucher' => $voucher,
        'totals' => [
            'subtotal' => number_format($subtotal, 0, ',', '.') . 'đ',
            'discount' => number_format($discount, 0, ',', '.') . 'đ',
            'total' => number_format($total, 0, ',', '.') . 'đ',
            'count' => count($cartItems),
        ]
    ];

    $this->loadView('Payment.Checkout', $data);
}


}