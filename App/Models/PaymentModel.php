<?php
require_once 'BaseModel.php';

class PaymentModel extends BaseModel
{
    /** 
     * Lấy danh sách phương thức thanh toán người dùng đã thêm 
     */
    public function getUserMethods($userId)
    {
        $sql = "SELECT DISTINCT card_brand FROM user_cards WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Thêm phương thức thanh toán mới
     */
    public function addMethod($userId, $brand, $holder, $last4, $expiry)
    {
        $sql = "INSERT INTO user_cards (user_id, card_holder, card_number_last4, expiry_date, card_brand, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$userId, $holder, $last4, $expiry, $brand]);
    }

    /**
     * Kiểm tra người dùng có thẻ này chưa
     */
    public function hasMethod($userId, $brand)
    {
        $sql = "SELECT COUNT(*) FROM user_cards WHERE user_id = ? AND card_brand = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId, $brand]);
        return $stmt->fetchColumn() > 0;
    }


}
