<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Models/CartModel.php';
require_once __DIR__ . '/../Models/PaymentModel.php';
require_once __DIR__ . '/../Models/VoucherModel.php';

class CheckoutController extends BaseController
{
    public function index()
    {
        global $title;
        $title = "Thanh Toán | Blossy";

        // ✅ Kiểm tra đăng nhập
        $userId = $_SESSION['user']['user_id'] ?? null;
        if (!$userId) {
            echo "<script>alert('Bạn cần đăng nhập để thanh toán!'); window.location.href='" . BASE_URL . "index.php?controller=auth&action=login';</script>";
            exit;
        }

        // ✅ Lấy thông tin người dùng
        $userModel = new UserModel();
        $user = $userModel->getUserById($userId);

        // ✅ Lấy ID giỏ hàng, nếu chưa có thì tạo mới
        $cartModel = new CartModel();
        $cartId = $cartModel->getOrCreateCart($userId);

        // ✅ Lấy danh sách sản phẩm trong giỏ hàng
        $cartItems = $cartModel->getCartItemsByUser($userId);
        if (!is_array($cartItems)) $cartItems = [];

        // ✅ Tính tổng tiền
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // ✅ Áp dụng mã voucher nếu có
        $voucherCode = $_GET['voucher'] ?? '';
        $discount = 0;
        $voucher = null;

        if ($voucherCode !== '') {
            $voucherModel = new VoucherModel();
            $voucher = $voucherModel->getActiveVoucher($voucherCode);

            if ($voucher && $subtotal >= $voucher['min_order_total']) {
                if ($voucher['type'] === 'percent') {
                    $discount = min($subtotal * ($voucher['value'] / 100), $voucher['max_discount']);
                } else {
                    $discount = $voucher['value'];
                }
            } else {
                $voucher = null; // Không hợp lệ
            }
        }

        $total = $subtotal - $discount;

        // ✅ Lấy phương thức thanh toán
        $paymentModel = new PaymentModel();
        $methods = $paymentModel->getUserMethods($userId);

        // ✅ Gom dữ liệu cho view
        $data = [
            'user' => $user,
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
