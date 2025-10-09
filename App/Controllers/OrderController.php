<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/OrderModel.php';
require_once __DIR__ . '/../Models/CartModel.php';
require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Models/UserModel.php';

class OrderController extends BaseController
{
    /**
     * Xem chi tiết đơn hàng
        */
    public function detail($id)
    {
        global $title;
        $title = "Chi tiết đơn hàng | Blossy";

        $orderModel = new OrderModel();
        $order = $orderModel->getOrderById($id);

        if (!$order) {
            echo "<script>alert('❌ Không tìm thấy đơn hàng!'); window.location.href='" . BASE_URL . "index.php?controller=auth&action=info';</script>";
            exit;
        }

        // ✅ Tạo mã đơn hàng nếu chưa có
        if (empty($order['code'])) {
            $order['code'] = 'OD' . str_pad($order['id'], 5, '0', STR_PAD_LEFT);
        }

        // ✅ Gán tổng tiền (grand_total) định dạng đẹp
        $order['total'] = number_format($order['grand_total'] ?? 0, 0, ',', '.') . 'đ';

        // ✅ Gán phương thức thanh toán viết hoa
        $order['payment'] = strtoupper($order['payment_method'] ?? 'COD');

        // ✅ Ngày giao dự kiến (3 ngày sau)
        $order['delivery_date'] = date('d/m/Y', strtotime('+3 days'));

        $orderItems = $orderModel->getOrderItems($id);

        $data = [
            'order' => $order,
            'items' => $orderItems
        ];

        $this->loadView('Order.OrderCompleted', $data);
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

        // ✅ Ưu tiên session Mua Ngay
        if (!empty($_SESSION['buy_now'])) {
            $cartItems = $_SESSION['buy_now'];
        } else {
            // Nếu không có → lấy từ DB
            $cartItems = $cartModel->getCartItemsByUser($userId);

            // 🔁 Nếu DB trống → fallback session cart
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

        // ✅ Lấy thông tin giảm giá và voucher từ form
        $discount = (float)($_POST['voucher_discount'] ?? 0);
        $voucherCode = $_POST['voucher_code'] ?? null;

        $shipping = 30000;
        $total = max(0, $subtotal - $discount + $shipping);

        // ✅ Phân bổ giảm giá cho từng sản phẩm (để hiển thị)
        if ($discount > 0 && $subtotal > 0) {
            $remainingDiscount = $discount;
            $count = count($cartItems);

            foreach ($cartItems as $index => &$item) {
                $lineTotal = $item['price'] * $item['quantity'];

                if ($index < $count - 1) {
                    $itemDiscount = round($discount * ($lineTotal / $subtotal));
                    $remainingDiscount -= $itemDiscount;
                } else {
                    // sản phẩm cuối nhận phần còn lại
                    $itemDiscount = max(0, $remainingDiscount);
                }

                $item['discount'] = $itemDiscount;
                $item['price_after'] = round(max(0, ($lineTotal - $itemDiscount) / $item['quantity']));
            }
        }
        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        $paymentStatus = ($paymentMethod !== 'cod') ? 'Đã thanh toán' : 'Chưa thanh toán';

        // ✅ Tạo đơn hàng mới
        $orderId = $orderModel->createOrder([
            'user_id' => $userId,
            'address_id' => 1,
            'status' => 'cho_xac_nhan',
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'subtotal' => $subtotal,
            'discount_total' => $discount,
            'shipping_fee' => $shipping,
            'grand_total' => $total,
            'voucher_code' => $voucherCode,
            'note' => 'Giao hàng tận nơi',
            'delivery_date' => date('Y-m-d', strtotime('+3 days'))
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
        unset($_SESSION['buy_now']); 

        // ✅ Lưu session để hiển thị trang Order Completed
        $_SESSION['last_order'] = [
            'order' => [
                'code' => 'OD' . str_pad($orderId, 5, '0', STR_PAD_LEFT),
                'payment' => strtoupper($_POST['payment_method'] ?? 'COD'),
                'subtotal' => number_format($subtotal, 0, ',', '.') . 'đ',
                'discount' => number_format($discount, 0, ',', '.') . 'đ',
                'shipping' => number_format($shipping, 0, ',', '.') . 'đ',
                'total' => number_format($total, 0, ',', '.') . 'đ',
                'voucher' => $voucherCode,
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
            echo "<script>window.location.href='index.php?controller=products&action=index';</script>";
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
