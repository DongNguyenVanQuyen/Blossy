<?php
require_once 'BaseModel.php';

class ProductModel extends BaseModel
{
    /** Lấy tất cả sản phẩm (có ảnh chính duy nhất) */
    public function getAll()
    {
        $sql = "SELECT p.*, img.url
                FROM products p
                LEFT JOIN product_images img 
                    ON p.id = img.product_id AND img.is_primary = 1
                WHERE p.is_active = 1
                GROUP BY p.id
                ORDER BY p.created_at DESC";
        return $this->query($sql);
    }

    /** Lấy chi tiết sản phẩm theo ID (kèm toàn bộ ảnh) */
    public function getById($id)
    {
        $sql = "SELECT p.*, i.stock, i.low_stock_threshold
                FROM products p
                LEFT JOIN inventory i ON p.id = i.product_id
                WHERE p.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) return null;

        // Lấy tất cả ảnh liên quan
        $imgSql = "SELECT url, is_primary 
                   FROM product_images 
                   WHERE product_id = ? 
                   ORDER BY sort_order ASC";
        $imgStmt = $this->conn->prepare($imgSql);
        $imgStmt->execute([$id]);
        $product['images'] = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

        return $product;
    }

    /** Lấy sản phẩm liên quan cùng category */
    public function getRelatedProducts($productId, $categoryId)
    {
        $sql = "SELECT p.*, pi.url
                FROM products p
                LEFT JOIN product_images pi 
                    ON p.id = pi.product_id AND pi.is_primary = 1
                WHERE p.category_id = ? 
                  AND p.id != ? 
                  AND p.is_active = 1
                GROUP BY p.id
                ORDER BY p.created_at DESC
                LIMIT 6";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$categoryId, $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Phân trang (chỉ ảnh chính) */
    public function getPaginated($limit, $offset)
    {
        $sql = "SELECT p.*, pi.url
                FROM products p
                LEFT JOIN product_images pi 
                    ON pi.product_id = p.id AND pi.is_primary = 1
                WHERE p.is_active = 1
                GROUP BY p.id
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Đếm tổng số sản phẩm */
    public function countAll()
    {
        $sql = "SELECT COUNT(*) as total 
                FROM products 
                WHERE is_active = 1";
        return $this->queryOne($sql)['total'];
    }

    /** Lọc sản phẩm (có fix ảnh trùng & LIMIT/OFFSET) */
    public function getFiltered(array $categories, array $colors, string $priceRange, int $limit, int $offset)
    {
        $sql = "SELECT p.*, i.stock, img.url
                FROM products p
                LEFT JOIN inventory i ON p.id = i.product_id
                LEFT JOIN product_images img 
                    ON p.id = img.product_id AND img.is_primary = 1
                WHERE p.is_active = 1";
        $params = [];

        // Lọc loại hoa
        if (!in_array('all', $categories) && !empty($categories)) {
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $sql .= " AND p.category_id IN ($placeholders)";
            $params = array_merge($params, $categories);
        }

        // Lọc màu sắc
        if (!empty($colors)) {
            $placeholders = implode(',', array_fill(0, count($colors), '?'));
            $sql .= " AND p.color IN ($placeholders)";
            $params = array_merge($params, $colors);
        }

        // Lọc giá
        if (!empty($priceRange)) {
            [$min, $max] = explode('-', $priceRange);
            $sql .= " AND p.price BETWEEN ? AND ?";
            $params[] = (int)$min;
            $params[] = (int)$max;
        }

        // Gom nhóm để không trùng sản phẩm
        $sql .= " GROUP BY p.id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);

        // Bind các tham số lọc
        foreach ($params as $index => $param) {
            $stmt->bindValue($index + 1, $param);
        }

        // Bind LIMIT & OFFSET
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Đếm tổng số sản phẩm khi lọc */
    public function countFiltered($categories, $colors, $priceRange)
    {
        $sql = "SELECT COUNT(DISTINCT p.id) as total
                FROM products p
                WHERE p.is_active = 1";
        $params = [];

        // Lọc loại hoa
        if (!empty($categories) && !in_array('all', $categories)) {
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $sql .= " AND p.category_id IN ($placeholders)";
            $params = array_merge($params, $categories);
        }

        // Lọc màu sắc
        if (!empty($colors)) {
            $placeholders = implode(',', array_fill(0, count($colors), '?'));
            $sql .= " AND p.color IN ($placeholders)";
            $params = array_merge($params, $colors);
        }

        // Lọc giá
        if (!empty($priceRange)) {
            [$min, $max] = explode('-', $priceRange);
            $sql .= " AND p.price BETWEEN ? AND ?";
            $params[] = (int)$min;
            $params[] = (int)$max;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
