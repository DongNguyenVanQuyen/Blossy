<?php
require_once 'BaseModel.php';

class AdminCustomerModel extends BaseModel
{
    /** Lấy danh sách khách hàng (phân trang) */
    public function getAll($limit, $offset)
    {
        $sql = "SELECT id, first_name, last_name, email, phone, gender, level, is_blocked, created_at
                FROM users
                Where role_id = 1
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /** Đếm tổng số khách hàng */
    public function countAll()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM users");
        return (int)$stmt->fetchColumn();
    }

    /** Cập nhật trạng thái (khoá / mở) */
    public function toggleStatus($id)
    {
        $sql = "UPDATE users 
                SET is_blocked = IF(is_blocked = 1, 0, 1),
                    updated_at = NOW()
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    /** Cập nhật tổng chi tiêu cho từng khách hàng */
    public function updateTotalSpent()
    {
        $sql = "
            UPDATE users u
            LEFT JOIN (
                SELECT user_id, SUM(grand_total) AS total
                FROM orders
                WHERE status = 'hoan_thanh'
                GROUP BY user_id
            ) o ON u.id = o.user_id
            SET u.total_spent = COALESCE(o.total, 0)
        ";
        return $this->conn->exec($sql);
    }

    /** Cập nhật hạng khách hàng dựa trên total_spent */
    public function updateCustomerLevels()
    {
        $sql = "
            UPDATE users
            SET level = CASE
                WHEN total_spent >= 10000000 THEN 'diamond'
                WHEN total_spent >= 5000000 THEN 'gold'
                WHEN total_spent >= 2000000 THEN 'silver'
                ELSE 'normal'
            END
        ";
        return $this->conn->exec($sql);
    }

}
