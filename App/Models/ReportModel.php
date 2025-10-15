<?php
require_once 'BaseModel.php';

class ReportModel extends BaseModel
{
    public function countTable(string $table): int
    {
        $allowed = ['products','orders','users','reviews','vouchers'];
        if (!in_array($table, $allowed, true)) return 0;
            if ($table === 'users') {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM users WHERE role_id = 1");
        } else {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM {$table}");
        }
            return (int)$stmt->fetchColumn();
    }

    public function getTotalRevenue(): int
    {
        $sql = "SELECT COALESCE(SUM(grand_total),0) 
                FROM orders 
                WHERE status = 'hoan_thanh'";
        return (int)$this->conn->query($sql)->fetchColumn();
    }

    public function getRecentOrders(int $limit = 10): array
    {
        $sql = "SELECT id,
                       CONCAT('OD', LPAD(id,5,'0')) AS code,
                       DATE_FORMAT(created_at,'%d/%m/%Y %H:%i') AS created_at_fmt,
                       status, payment_method, grand_total
                FROM orders
                ORDER BY created_at DESC
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Top sp theo số lượng bán (dựa vào order_items)
    public function getTopProducts(int $limit = 8): array
    {
        $sql = "SELECT p.id, p.name,
                    COALESCE(SUM(oi.quantity),0) AS qty_sold,
                    COALESCE(SUM(oi.line_total),0) AS revenue
                FROM products p
                LEFT JOIN order_items oi ON oi.product_id = p.id
                LEFT JOIN orders o ON o.id = oi.order_id AND o.status = 'hoan_thanh'
                GROUP BY p.id, p.name
                ORDER BY qty_sold DESC
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }



    // Doanh thu theo tháng gần đây
    public function getRevenueByMonth(int $months = 6): array
    {
        $sql = "SELECT DATE_FORMAT(created_at,'%m/%Y') AS mth,
                       COALESCE(SUM(grand_total),0) AS revenue
                FROM orders
                WHERE status='hoan_thanh'
                  AND created_at >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
                GROUP BY DATE_FORMAT(created_at,'%Y-%m')
                ORDER BY MIN(created_at) ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
