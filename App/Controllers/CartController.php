<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Models/CartModel.php';

class CartController extends BaseController
{
    /** Hiển thị trang giỏ hàng */
    public function index()
    {
        global $title;
        $title = "Giỏ hàng | Blossy";

        $cart = $_SESSION['cart'] ?? [];

        // Nếu người dùng đã đăng nhập → lấy từ DB
        if (isset($_SESSION['user']['user_id'])) {
            $cartModel = new CartModel();
            $cart = $cartModel->getCartItemsByUser($_SESSION['user']['user_id']);
        }

        $this->loadView('Cart.index', ['cart' => $cart]);
    }

    /** Thêm sản phẩm vào giỏ */
    public function add()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
            return;
        }

        $productId = (int)$_POST['product_id'];
        $quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        $productModel = new ProductModel();
        $cartModel = new CartModel();
        $product = $productModel->getById($productId);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
            return;
        }

        $stock = $product['stock'] ?? 0;
        if ($stock <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm đã hết hàng']);
            return;
        }

        if ($quantity > $stock) $quantity = $stock;

        // ✅ Nếu người dùng đăng nhập → lưu SQL
        if (isset($_SESSION['user']['user_id'])) {
            $userId = $_SESSION['user']['user_id'];
            $cartId = $cartModel->getOrCreateCart($userId);
            $cartModel->addItem($cartId, $productId, $quantity);
        }

        // ✅ Đồng thời lưu session (hiển thị nhanh)
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
            if ($_SESSION['cart'][$productId]['quantity'] > $stock)
                $_SESSION['cart'][$productId]['quantity'] = $stock;
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'price_old' => $product['compare_at_price'],
                'image_url' => $product['url'] ?? '',
                'stock' => $stock,
                'quantity' => $quantity
            ];
        }

        echo json_encode(['success' => true, 'message' => 'Đã thêm vào giỏ hàng']);
    }

    /** ✅ Cập nhật số lượng (AJAX) */
   public function update()
{
    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_POST['product_id'], $_POST['quantity'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
        exit;
    }

    $id = (int)$_POST['product_id'];
    $qty = max(1, (int)$_POST['quantity']);

    // Cập nhật session
    if (isset($_SESSION['cart'][$id])) {
        $stock = $_SESSION['cart'][$id]['stock'] ?? 1;
        $_SESSION['cart'][$id]['quantity'] = min($qty, $stock);
    }

    // Cập nhật DB
    if (isset($_SESSION['user']['user_id'])) {
        $cartModel = new CartModel();
        $userId = $_SESSION['user']['user_id'];
        $cartId = $cartModel->getOrCreateCart($userId);

        $stmt = $cartModel->conn->prepare("
            UPDATE cart_items 
            SET quantity = ?, added_at = NOW() 
            WHERE cart_id = ? AND product_id = ?
        ");
        $stmt->execute([$qty, $cartId, $id]);
    }

    // Tính lại tổng
    $subtotal = 0;
    $totalItems = 0;
    foreach ($_SESSION['cart'] ?? [] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
        $totalItems += $item['quantity'];
    }

    echo json_encode([
        'success' => true,
        'subtotal' => number_format($subtotal, 0, ',', '.') . 'đ',
        'totalItems' => $totalItems
    ]);
    exit; // 🔥 Thêm dòng này để dừng hoàn toàn output
}


  /** Xóa 1 sản phẩm khỏi giỏ hàng (AJAX) */
    public function remove()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_POST['product_id'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu mã sản phẩm']);
            exit;
        }

        $id = (int)$_POST['product_id'];

        // Xóa trong session
        unset($_SESSION['cart'][$id]);

        // Nếu user login → xóa trong DB
        if (isset($_SESSION['user']['user_id'])) {
            $cartModel = new CartModel();
            $userId = $_SESSION['user']['user_id'];
            $cartId = $cartModel->getOrCreateCart($userId);
            $cartModel->removeItem($cartId, $id);
        }

        // Tính lại tổng
        $subtotal = 0;
        $totalItems = 0;
        foreach ($_SESSION['cart'] ?? [] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            $totalItems += $item['quantity'];
        }

        echo json_encode([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
            'subtotal' => number_format($subtotal, 0, ',', '.') . 'đ',
            'totalItems' => $totalItems
        ]);
        exit;
    }


    /** Xóa toàn bộ giỏ */
    public function clear()
    {
        header('Content-Type: application/json; charset=utf-8');
        unset($_SESSION['cart']);

        if (isset($_SESSION['user']['user_id'])) {
            $cartModel = new CartModel();
            $userId = $_SESSION['user']['user_id'];
            $cartId = $cartModel->getOrCreateCart($userId);
            $cartModel->clearCart($cartId);
        }

        echo json_encode(['success' => true]);
    }
}
