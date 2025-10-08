<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/OrderModel.php';
require_once __DIR__ . '/../Models/CartModel.php';
require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Models/UserModel.php';

class OrderController extends BaseController
{
    /**
     * Chi tiết đơn hàng (xem lại)
     */
    public function detail($id)
    {
        global $title;
        $title = "Chi tiết đơn hàng | Blossy";

        $orderModel = new OrderModel();
        $order = $orderModel->getOrderById($id);

        if (!$order) {
            echo "<script>alert('❌ Không tìm thấy đơn hàng!'); window.location.href='" . BASE_URL . "index.php?controller=auth&action=Info';</script>";
            exit;
        }

        $orderItems = $orderModel->getOrderItems($id);
        $data = [
            'order' => $order,
            'items' => $orderItems
        ];

        $this->loadView('Order.OrderComplete', $data);
    }

    /**
     * Xử lý khi người dùng bấm "Thanh Toán"
     */
    public function complete()
    {
        global $title;
        $title = "Đặt hàng thành công | Blossy";
        header('Content-Type: application/json; charset=utf-8');

        // ✅ Kiểm tra đăng nhập
        if (!isset($_SESSION['user']['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập trước khi thanh toán.']);
            exit;
        }

        $userId = $_SESSION['user']['user_id'];
        $cartModel = new CartModel();
        $productModel = new ProductModel();
        $orderModel = new OrderModel();

        // ✅ Lấy giỏ hàng từ DB
        $cartItems = $cartModel->getCartItemsByUser($userId);

        // 🔁 Nếu DB trống → lấy từ session
        if (empty($cartItems) && !empty($_SESSION['cart'])) {
            $cartItems = array_map(function ($item) {
                return [
                    'product_id' => $item['id'] ?? $item['product_id'],
                    'name'       => $item['name'],
                    'price'      => $item['price'],
                    'quantity'   => $item['quantity'],
                    'image_url'  => $item['image_url'] ?? '',
                    'stock'      => $item['stock'] ?? 1
                ];
            }, $_SESSION['cart']);
        }

        // ✅ Nếu vẫn trống thì báo lỗi
        if (empty($cartItems)) {
            echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống, không thể thanh toán!']);
            exit;
        }

        // ✅ Kiểm tra sản phẩm hợp lệ + tồn kho
        foreach ($cartItems as $item) {
            $pid = $item['product_id'] ?? 0;
            $product = $productModel->getById($pid);

            if (!$product || !$product['is_active']) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ hoặc đã bị xóa.']);
                exit;
            }

            if (($product['stock'] ?? 0) < $item['quantity']) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm "' . $product['name'] . '" không đủ hàng.']);
                exit;
            }
        }

        // ✅ Tính tổng tiền
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discount = 0;
        $shipping = 30000; // tạm phí ship cố định
        $total = $subtotal - $discount + $shipping;

        // ✅ Tạo đơn hàng mới
        $orderId = $orderModel->createOrder([
            'user_id' => $userId,
            'address_id' => 1,
            'status' => 'cho_xac_nhan',
            'payment_method' => $_POST['payment_method'] ?? 'cod',
            'payment_status' => 'chua_thanh_toan',
            'subtotal' => $subtotal,
            'discount_total' => $discount,
            'shipping_fee' => $shipping,
            'grand_total' => $total,
            'voucher_code' => $_POST['voucher'] ?? null,
            'note' => 'Giao hàng tận nơi'
        ]);

        // ✅ Lưu chi tiết sản phẩm vào order_items
        foreach ($cartItems as $product) {
            $orderModel->addOrderItem($orderId, $product);
        }

        // ✅ Giảm tồn kho sản phẩm
        foreach ($cartItems as $item) {
            $productModel->reduceStock($item['product_id'], $item['quantity']);
        }

        // ✅ Xóa giỏ hàng sau khi đặt
        $cartModel->clearCart($userId);
        unset($_SESSION['cart']);

        // ✅ Lưu session hiển thị trang "Order Completed"
        $_SESSION['last_order'] = [
            'order' => [
                'code' => 'OD' . str_pad($orderId, 5, '0', STR_PAD_LEFT),
                'payment' => strtoupper($_POST['payment_method'] ?? 'COD'),
                'total' => number_format($total, 0, ',', '.') . 'đ',
                'status' => 'Chờ xác nhận',
                'delivery_date' => date('d/m/Y', strtotime('+3 days'))
            ],
            'items' => $cartItems
        ];

        echo json_encode([
            'success' => true,
            'redirect' => 'index.php?controller=order&action=showCompleted'
        ]);
        exit;
    }

    /**
     * Hiển thị trang hoàn tất đơn hàng
     */
    public function showCompleted()
    {
        global $title;
        $title = "Đặt hàng thành công | Blossy";

        if (isset($_SESSION['last_order'])) {
            $order = $_SESSION['last_order']['order'];
            $items = $_SESSION['last_order']['items'];

            $this->loadView('Order.OrderCompleted', [
                'order' => $order,
                'items' => $items
            ]);
        } else {
          
            exit;
        }
    }

    /** Xóa session đơn hàng */
    public function clearSession()
    {
        unset($_SESSION['last_order']);
        echo json_encode(['success' => true]);
        exit;
    }
}
