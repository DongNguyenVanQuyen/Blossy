<?php
require_once __DIR__ . '/BaseModel.php';

class OrderModel extends BaseModel{

    public function getOrderById($id): ?array
    {
        if (empty($id) || !is_numeric($id)) {
            return null;
        }

        $stmt = $this->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        return $order ?: null;
    }
    public function getOrderItems($orderId): array
    {
        $sql = "SELECT 
                    oi.id,
                    oi.product_id,
                    oi.product_name AS name,
                    oi.unit_price AS price,
                    oi.quantity,
                    oi.line_total,
                    p.compare_at_price,
                    pi.url AS image_url
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                LEFT JOIN product_images pi 
                    ON p.id = pi.product_id AND pi.is_primary = 1
                WHERE oi.order_id = ?";
        $stmt = $this->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /** Tạo mới đơn hàng và trả về ID của đơn hàng vừa tạo */
     public function createOrder($data)
    {
        $sql = "INSERT INTO orders (
            user_id, address_id, status, payment_method, payment_status, 
            subtotal, discount_total, shipping_fee, grand_total, voucher_code, note, delivery_date,
            created_at, updated_at
        ) VALUES (
            :user_id, :address_id, :status, :payment_method, :payment_status,
            :subtotal, :discount_total, :shipping_fee, :grand_total, :voucher_code, :note, :delivery_date,
            NOW(), NOW()
        )";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);
        return $this->conn->lastInsertId();
    }

    /** Thêm từng sản phẩm vào bảng order_items */
   public function addOrderItem($orderId, $product)
    {
        $sql = "INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, line_total)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        // ✅ Lấy đúng ID sản phẩm
        $pid = $product['product_id'] ?? $product['id'] ?? null;
        if (!$pid) {
            throw new Exception("Thiếu product_id khi thêm vào order_items");
        }

        $line_total = $product['price'] * $product['quantity'];

        $stmt->execute([
            $orderId,
            $pid,
            $product['name'],
            $product['price'],
            $product['quantity'],
            $line_total
        ]);
    }
}
