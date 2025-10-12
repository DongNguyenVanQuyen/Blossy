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
        ini_set('display_errors', 0);

        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user']['user_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng'
            ]);
            exit;
        }

        if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
            exit;
        }

        $productId = (int)$_POST['product_id'];
        $quantity  = isset($_POST['quantity']) && is_numeric($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        $productModel = new ProductModel();
        $cartModel    = new CartModel();
        $product      = $productModel->getById($productId);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
            exit;
        }

        $stock = $product['stock'] ?? 0;
        if ($stock <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm đã hết hàng']);
            exit;
        }

        if ($quantity > $stock) $quantity = $stock;

        $userId = $_SESSION['user']['user_id'];
        $cartId = $cartModel->getOrCreateCart($userId);
        $cartModel->addItem($cartId, $productId, $quantity);

        // session cache
        $_SESSION['cart'][$productId]['quantity'] = ($_SESSION['cart'][$productId]['quantity'] ?? 0) + $quantity;

        echo json_encode(['success' => true, 'message' => 'Đã thêm vào giỏ hàng']);
        exit;
    }


    /** ✅ Cập nhật số lượng (AJAX) */
   public function update()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_POST['product_id'], $_POST['quantity'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
            exit;
        }

        $id  = (int)$_POST['product_id'];
        $qty = max(1, (int)$_POST['quantity']);

        require_once __DIR__ . '/../Models/ProductModel.php';
        $productModel = new ProductModel();
        $product = $productModel->getById($id);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
            exit;
        }

        $stock = (int)$product['stock'];
        if ($stock <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm đã hết hàng']);
            exit;
        }

        // 🔹 Giới hạn không vượt quá tồn kho
        if ($qty > $stock) {
            $qty = $stock;
            $message = "Số lượng vượt quá tồn kho (chỉ còn $stock)";
        } else {
            $message = "Đã cập nhật số lượng";
        }

        // 🔹 Cập nhật session
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] = $qty;
        }

        // 🔹 Cập nhật DB nếu user đăng nhập
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

        // 🔹 Tính lại tổng
        $subtotal = 0;
        $totalItems = 0;
        foreach ($_SESSION['cart'] ?? [] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            $totalItems += $item['quantity'];
        }

        echo json_encode([
            'success' => true,
            'message' => $message,
            'quantity' => $qty,
            'subtotal' => number_format($subtotal, 0, ',', '.') . 'đ',
            'totalItems' => $totalItems
        ]);
        exit;
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
