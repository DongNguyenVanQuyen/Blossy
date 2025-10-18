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
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Không tìm thấy đơn hàng!'
            ];
            header("Location: " . BASE_URL . "index.php?controller=auth&action=info");
            exit;
        }

        // Tạo mã đơn hàng nếu chưa có
        if (empty($order['code'])) {
            $order['code'] = 'OD' . str_pad($order['id'], 5, '0', STR_PAD_LEFT);
        }

        // Tổng tiền, phương thức, ngày giao
        $order['total'] = number_format($order['grand_total'] ?? 0, 0, ',', '.') . 'đ';
        $order['payment'] = strtoupper($order['payment_method'] ?? 'COD');
        $order['delivery_date'] = !empty($order['delivery_date'])
            ? date('d/m/Y', strtotime($order['delivery_date']))
            : date('d/m/Y', strtotime(($order['created_at'] ?? 'now') . ' +3 days'));

        // Lấy danh sách sản phẩm
        $orderItems = $orderModel->getOrderItems($id);

        // Xử lý giá cũ / mới
        foreach ($orderItems as &$item) {
            $price = $item['price'] ?? $item['unit_price'] ?? 0;
            $discount = $item['discount'] ?? 0;
            $old = $item['compare_at_price'] ?? $item['old_price'] ?? $price;
            $quantity = $item['quantity'] ?? 1;

            if ($discount == 0 && !empty($order['discount_total']) && !empty($order['subtotal']) && $order['subtotal'] > 0) {
                $share = ($price * $quantity) / $order['subtotal'];
                $discount = round($order['discount_total'] * $share, 0);
            }

            $item['new_price'] = max(0, $price - ($discount / $quantity));
            $item['old_price'] = $old;
        }
        unset($item);

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

        if (!isset($_SESSION['user']['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập trước khi thanh toán.']);
            exit;
        }

        $user = $_SESSION['user'];
        $userId = $user['user_id'];
        $cartModel = new CartModel();
        $productModel = new ProductModel();
        $orderModel = new OrderModel();

        // ✅ Ưu tiên "Mua ngay"
        if (!empty($_SESSION['buy_now'])) {
            $cartItems = $_SESSION['buy_now'];
        } else {
            $cartItems = $cartModel->getCartItemsByUser($userId);
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

        if (empty($cartItems)) {
            echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống, không thể thanh toán!']);
            exit;
        }

        // ✅ Gộp sản phẩm trùng product_id
        $mergedItems = [];
        foreach ($cartItems as $item) {
            $pid = $item['product_id'];
            if (!isset($mergedItems[$pid])) {
                $mergedItems[$pid] = $item;
            } else {
                $mergedItems[$pid]['quantity'] += $item['quantity'];
            }
        }
        $cartItems = array_values($mergedItems);

        // ✅ Kiểm tra tồn kho
        foreach ($cartItems as $item) {
            $pid = $item['product_id'];
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

        // ✅ Tính toán tổng
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discount = (float)($_POST['voucher_discount'] ?? 0);
        $voucherCode = $_POST['voucher_code'] ?? null;
        $shipping = 30000;
        $total = max(0, $subtotal - $discount + $shipping);

        // ✅ Phân bổ giảm giá đều cho từng sản phẩm
        if ($discount > 0 && $subtotal > 0) {
            foreach ($cartItems as &$item) {
                $share = ($item['price'] * $item['quantity']) / $subtotal;
                $item['discount'] = round($discount * $share, 0);
                $item['new_price'] = max(0, $item['price'] - ($item['discount'] / $item['quantity']));
            }
            unset($item);
        }

        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        $paymentStatus = ($paymentMethod !== 'cod') ? 'Đã thanh toán' : 'Chưa thanh toán';
        $deliveryDate = date('Y-m-d', strtotime('+3 days'));

        // ✅ Lưu đơn hàng
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
            'delivery_date' => $deliveryDate
        ]);

        // ✅ Ghi nhận voucher
        if (!empty($voucherCode)) {
            require_once __DIR__ . '/../Models/VoucherModel.php';
            $voucherModel = new VoucherModel();
            $voucher = $voucherModel->getActiveVoucher($voucherCode);

            if ($voucher) {
                $voucherModel->recordUsage($voucher['id'], $userId, $orderId);
            }
        }

        // ✅ Lưu sản phẩm trong đơn
        foreach ($cartItems as $product) {
            $orderModel->addOrderItem($orderId, $product);
        }

        // ✅ Trừ tồn kho
        foreach ($cartItems as $item) {
            $productModel->reduceStock($item['product_id'], $item['quantity']);
        }

        // ✅ Dọn giỏ hàng
        $cartModel->clearCart($userId);
        unset($_SESSION['cart'], $_SESSION['buy_now']);

        // ✅ Lưu session đơn hàng cuối cùng
        $_SESSION['last_order'] = [
            'order' => [
                'code' => 'OD' . str_pad($orderId, 5, '0', STR_PAD_LEFT),
                'payment' => strtoupper($paymentMethod),
                'payment_status' => $paymentStatus,
                'subtotal' => number_format($subtotal, 0, ',', '.') . 'đ',
                'discount' => number_format($discount, 0, ',', '.') . 'đ',
                'shipping' => number_format($shipping, 0, ',', '.') . 'đ',
                'total' => number_format($total, 0, ',', '.') . 'đ',
                'voucher' => $voucherCode,
                'status' => 'cho_xac_nhan',
                'delivery_date' => $deliveryDate
            ],
            'items' => $cartItems
        ];

        // ✅ Phản hồi cho AJAX
        echo json_encode([
            'success' => true,
            'redirect' => 'index.php?controller=order&action=showCompleted'
        ]);
        exit;
    }


    public function showCompleted()
    {
        global $title;
        $title = "Đặt hàng thành công | Blossy";

        if (!isset($_SESSION['last_order'])) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Không tìm thấy đơn hàng!'
            ];
            header("Location: index.php?controller=products&action=index");
            exit;
        }

        $order = $_SESSION['last_order']['order'] ?? [];
        $items = $_SESSION['last_order']['items'] ?? [];
        require_once __DIR__ . '/../Includes/Mailer.php';

        // Sau khi INSERT đơn hàng thành công:
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            require_once __DIR__ . '/../Includes/Mailer.php';

            $orderData = [
                'code' => $order['code'],
                'customer_name' => $user['first_name'] . ' ' . $user['last_name'],
                'grand_total' => (int)str_replace(['đ', '.', ','], '', $order['total']),
                'payment_method' => $order['payment'],
                'address' => $user['address'] ?? 'Không xác định'
            ];

            sendOrderConfirmation($user['email'], $orderData, $items);
        }



        $lastProductId = null;
        $lastOrderItemId = null;

        if (!empty($items) && isset($items[0]['product_id'])) {
            $lastProductId = $items[0]['product_id'];
            $lastOrderItemId = $items[0]['id'] ?? null;
        }

        $this->loadView('Order.OrderCompleted', [
            'order' => $order,
            'items' => $items,
            'lastProductId' => $lastProductId,
            'lastOrderItemId' => $lastOrderItemId
        ]);
    }

    public function clearSession()
    {
        unset($_SESSION['last_order']);
        echo json_encode(['success' => true]);
        exit;
    }

    // 🔹 Mở form đánh giá sản phẩm trong đơn hàng
    public function reviewForm()
    {
        global $title;
        $title = "Đánh giá sản phẩm | Blossy";

        $productId = $_GET['id'] ?? null;
        $orderItemId = $_GET['order_item_id'] ?? null;

        if (!$productId || !$orderItemId) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Thiếu thông tin sản phẩm hoặc đơn hàng!'
            ];
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $productModel = new ProductModel();
        $product = $productModel->getById($productId);

        if (!$product) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Không tìm thấy sản phẩm!'
            ];
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $data = [
            'product' => $product,
            'order_item_id' => $orderItemId,
        ];

        $this->loadView('Order.ReviewForm', $data);
    }
}
