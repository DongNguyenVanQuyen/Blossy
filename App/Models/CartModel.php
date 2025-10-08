<?php
require_once 'BaseModel.php';

class CartModel extends BaseModel
{
    /** 
     * Lấy giỏ hàng của người dùng, nếu chưa có thì tạo mới 
     */
    public function getOrCreateCart($userId)
    {
        $sql = "SELECT id FROM carts WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart) {
            return $cart['id'];
        } else {
            $insert = $this->conn->prepare("INSERT INTO carts (user_id, updated_at) VALUES (?, NOW())");
            $insert->execute([$userId]);
            return $this->conn->lastInsertId();
        }
    }

    /** 
     * Thêm sản phẩm vào giỏ hàng 
     * Nếu đã có → cộng dồn số lượng
     */
    public function addItem($cartId, $productId, $quantity)
{
    $sql = "SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$cartId, $productId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // ✅ Không tăng số lượng, chỉ cập nhật thời gian
        $update = $this->conn->prepare("
            UPDATE cart_items 
            SET added_at = NOW() 
            WHERE id = ?
        ");
        $update->execute([$item['id']]);
    } else {
        // Thêm mới sản phẩm
        $insert = $this->conn->prepare("
            INSERT INTO cart_items (cart_id, product_id, quantity, added_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $insert->execute([$cartId, $productId, $quantity]);
    }

    // Cập nhật thời gian trong bảng carts
    $this->conn->prepare("UPDATE carts SET updated_at = NOW() WHERE id = ?")->execute([$cartId]);
}

    /** 
     * Lấy tất cả sản phẩm trong giỏ hàng theo user_id 
     */
   public function getCartItemsByUser($userId)
{
    $sql = "SELECT 
                p.id AS id,
                ci.product_id, 
                ci.quantity, 
                ci.added_at,
                p.name, 
                p.price, 
                p.compare_at_price as price_old,
                img.url AS image_url,
                i.stock
            FROM carts c
            JOIN cart_items ci ON c.id = ci.cart_id
            JOIN products p ON ci.product_id = p.id
            LEFT JOIN inventory i ON p.id = i.product_id
            LEFT JOIN product_images img 
                ON p.id = img.product_id AND img.is_primary = 1
            WHERE c.user_id = ?
            GROUP BY p.id     -- ✅ chỉ lấy 1 dòng duy nhất cho mỗi sản phẩm
            ORDER BY ci.added_at DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    /**
     * Xóa 1 sản phẩm khỏi giỏ hàng
     */
    public function removeItem($cartId, $productId)
    {
        $sql = "DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$cartId, $productId]);
    }

    /**
     * Xóa toàn bộ sản phẩm trong giỏ hàng
     */
    public function clearCart($cartId)
    {
        $sql = "DELETE FROM cart_items WHERE cart_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$cartId]);
    }
}
