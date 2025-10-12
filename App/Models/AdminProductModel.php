<?php
require_once 'BaseModel.php';

class AdminProductModel extends BaseModel
{
    /** Lấy tất cả sản phẩm (kèm ảnh, tồn kho, danh mục) */
    public function getAll()
    {
        $sql = "SELECT p.*, 
                    c.name AS category_name,
                    i.stock,
                    (
                        SELECT url FROM product_images 
                        WHERE product_id = p.id AND is_primary = 1 
                        ORDER BY sort_order ASC LIMIT 1
                    ) AS image_url
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN inventory i ON i.product_id = p.id
                GROUP BY p.id
                ORDER BY p.created_at DESC";
        return $this->query($sql);
    }

    /** Lấy toàn bộ ảnh của sản phẩm */
    public function getImagesByProduct($productId)
    {
        $sql = "SELECT id, url, is_primary FROM product_images 
                WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Lấy 1 sản phẩm */
    public function getById($id)
    {
        $sql = "SELECT p.*, i.stock, i.low_stock_threshold
                FROM products p
                LEFT JOIN inventory i ON i.product_id = p.id
                WHERE p.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Thêm sản phẩm mới */
    public function insert($data)
    {
        $sql = "INSERT INTO products (category_id, name, color, slug, season, description, price, compare_at_price, is_active, created_at)
                VALUES (:category_id, :name, :color, :slug, :season, :description, :price, :compare_at_price, :is_active, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);
        return $this->conn->lastInsertId();
    }

    /** Cập nhật sản phẩm */
    public function update($id, $data)
    {
        $data['id'] = $id;
        $sql = "UPDATE products 
                SET category_id=:category_id, name=:name, color=:color, slug=:slug, 
                    season=:season, description=:description, price=:price, 
                    compare_at_price=:compare_at_price, is_active=:is_active, updated_at=NOW()
                WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    /** Xóa sản phẩm */
    public function delete($id)
    {
        $this->conn->prepare("DELETE FROM product_images WHERE product_id=?")->execute([$id]);
        $this->conn->prepare("DELETE FROM inventory WHERE product_id=?")->execute([$id]);
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id=?");
        return $stmt->execute([$id]);
    }

    /** Xóa ảnh chính cũ */
    public function deletePrimaryImage($productId)
    {
        $sql = "DELETE FROM product_images WHERE product_id = ? AND is_primary = 1";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$productId]);
    }
public function deleteAllImages($productId)
{
    $stmt = $this->conn->prepare("DELETE FROM product_images WHERE product_id = ?");
    return $stmt->execute([$productId]);
}

    /** Lưu ảnh sản phẩm */
public function saveImage($productId, $url, $isPrimary = 0, $sortOrder = 1)
{
    try {
        $sql = "INSERT INTO product_images (product_id, url, is_primary, sort_order, created_at)
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $ok = $stmt->execute([$productId, $url, $isPrimary, $sortOrder]);

        if (!$ok) {
            $errorInfo = $stmt->errorInfo();
            error_log("❌ Lỗi lưu ảnh: " . print_r($errorInfo, true));
        } else {
            error_log("✅ Đã lưu ảnh mới cho sản phẩm #$productId - URL: $url");
        }

        return $ok;
    } catch (PDOException $e) {
        error_log("❌ PDO Exception: " . $e->getMessage());
        return false;
    }
}


    /** Cập nhật hoặc thêm tồn kho */
    public function saveInventory($productId, $stock, $threshold)
    {
        $check = $this->readitem("SELECT * FROM inventory WHERE product_id=?", [$productId]);
        if (!empty($check)) {
            $sql = "UPDATE inventory SET stock=?, low_stock_threshold=?, updated_at=NOW() WHERE product_id=?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$stock, $threshold, $productId]);
        } else {
            $sql = "INSERT INTO inventory (product_id, stock, low_stock_threshold, updated_at)
                    VALUES (?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$productId, $stock, $threshold]);
        }
    }

    /** Đếm tổng sản phẩm */
    public function countAll(): int
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM products");
        return (int)$stmt->fetchColumn();
    }

    /** Lấy sản phẩm có phân trang */
    public function getPaginated(int $limit, int $offset): array
    {
        $sql = "SELECT p.*, c.name AS category_name, i.stock,
                (SELECT url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS image_url
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN inventory i ON i.product_id = p.id
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Sinh slug mới nếu trùng */
    public function ensureUniqueSlug($slug, $id = null)
    {
        $newSlug = $slug;
        $count = 1;
        do {
            $sql = "SELECT id FROM products WHERE slug = ?" . ($id ? " AND id != ?" : "");
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($id ? [$newSlug, $id] : [$newSlug]);
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($exists) {
                $newSlug = $slug . '-' . $count++;
            } else break;
        } while (true);
        return $newSlug;
    }
}
