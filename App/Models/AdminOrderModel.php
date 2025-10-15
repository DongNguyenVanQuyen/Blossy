<?php
require_once 'BaseModel.php';

class AdminOrderModel extends BaseModel
{
    /** Lấy danh sách đơn hàng có phân trang */
    public function getAll($limit, $offset)
    {
        $sql = "SELECT o.*, u.first_name, u.last_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id)
    {
        $sql = "SELECT o.*, u.first_name, u.last_name 
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($orderId)
    {
        $sql = "SELECT oi.*, p.name, p.price,p.compare_at_price AS old_price, img.url AS image_url
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                LEFT JOIN product_images img ON p.id = img.product_id AND img.is_primary = 1
                WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /** Đếm tổng đơn hàng */
    public function countAll()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM orders");
        return (int)$stmt->fetchColumn();
    }

    /** ✅ Cập nhật trạng thái đơn */
public function updateStatus($id, $status)
{
    $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(1, $status, PDO::PARAM_STR);
    $stmt->bindValue(2, (int)$id, PDO::PARAM_INT);

    $ok = $stmt->execute();

    // ✅ GHI LẠI LOG DEBUG (đặt ở đây)
    error_log("Update order id={$id} => status={$status} | rows=" . $stmt->rowCount());

    return $ok && $stmt->rowCount() > 0;
}

}
