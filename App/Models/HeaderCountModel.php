<?php
require_once 'BaseModel.php';

class HeaderCountModel extends BaseModel
{
    /** Đếm số lượng sản phẩm yêu thích của user */
    public function getFavoriteCount($userId)
    {
        $sql = "SELECT COUNT(*) AS total FROM favorites WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['total'] : 0;
    }

    /** Đếm số lượng sản phẩm trong giỏ hàng của user */
    public function getCartCount($userId)
    {
        // Lấy cart_id
        $stmt = $this->conn->prepare("SELECT id FROM carts WHERE user_id = ?");
        $stmt->execute([$userId]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cart) return 0;

        // Đếm số item
        $stmt2 = $this->conn->prepare("SELECT COUNT(*) AS total FROM cart_items WHERE cart_id = ?");
        $stmt2->execute([$cart['id']]);
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['total'] : 0;
    }
}
