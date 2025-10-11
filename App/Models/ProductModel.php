<?php
require_once 'BaseModel.php';

class ProductModel extends BaseModel
{
    /** Lấy tất cả sản phẩm (có ảnh chính duy nhất) */
    public function getAll()
    {
        $sql = "SELECT p.*, img.url, c.name AS category_name, i.stock
                FROM products p
                LEFT JOIN product_images img 
                    ON p.id = img.product_id AND img.is_primary = 1
                LEFT JOIN inventory i ON p.id = i.product_id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1
                GROUP BY p.id
                ORDER BY p.created_at DESC";
        return $this->query($sql);
    }

    /** Lấy chi tiết sản phẩm theo ID (kèm toàn bộ ảnh) */
     public function getById($id)
    {
        $sql = "SELECT p.*, i.stock, i.low_stock_threshold, c.name AS category_name, 
               img.url, COALESCE(ROUND(AVG(r.rating),1), 5) AS rating
        FROM products p
        LEFT JOIN inventory i ON p.id = i.product_id
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN product_images img 
            ON p.id = img.product_id AND img.is_primary = 1
        LEFT JOIN reviews r ON r.product_id = p.id AND r.is_approved = 1
        WHERE p.id = ?
        GROUP BY p.id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) return null;

        // Lấy toàn bộ ảnh liên quan
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
        $sql = "SELECT p.*, pi.url, c.name AS category_name, i.stock,
               COALESCE(ROUND(AVG(r.rating),1), 5) AS rating
        FROM products p
        LEFT JOIN product_images pi 
            ON p.id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN inventory i ON p.id = i.product_id
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN reviews r ON r.product_id = p.id AND r.is_approved = 1
        WHERE p.category_id = ? 
          AND p.id != ? 
          AND p.is_active = 1
        GROUP BY p.id
        ORDER BY p.created_at DESC
        LIMIT 4";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$categoryId, $productId]);
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

    /** Lọc sản phẩm (thêm category và tồn kho) */
    public function getFiltered(array $categories, array $colors, string $priceRange, int $limit, int $offset, string $keyword = '')
    {
        $sql = "SELECT p.*, i.stock, img.url, c.name AS category_name,
               COALESCE(ROUND(AVG(r.rating),1), 5) AS rating
        FROM products p
        LEFT JOIN inventory i ON p.id = i.product_id
        LEFT JOIN product_images img 
            ON p.id = img.product_id AND img.is_primary = 1
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN reviews r ON r.product_id = p.id AND r.is_approved = 1
        WHERE p.is_active = 1";

        $params = [];

        // --- Lọc loại hoa ---
        if (!in_array('all', $categories) && !empty($categories)) {
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $sql .= " AND p.category_id IN ($placeholders)";
            $params = array_merge($params, $categories);
        }

        // --- Lọc màu sắc ---
        if (!in_array('all', $colors) && !empty($colors)) {
            $placeholders = implode(',', array_fill(0, count($colors), '?'));
            $sql .= " AND p.color IN ($placeholders)";
            $params = array_merge($params, $colors);
        }

        // --- Lọc giá ---
        if (!empty($priceRange)) {
            [$min, $max] = explode('-', $priceRange);
            $sql .= " AND p.price BETWEEN ? AND ?";
            $params[] = (int)$min;
            $params[] = (int)$max;
        }

        // --- Lọc theo từ khóa ---
        if (!empty($keyword)) {
            $sql .= " AND (p.name COLLATE utf8mb4_unicode_ci LIKE ? 
                        OR p.description COLLATE utf8mb4_unicode_ci LIKE ?)";
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
        }

        // --- Gom nhóm, sắp xếp, phân trang ---
        $sql .= " GROUP BY p.id
                  ORDER BY p.created_at DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);

        $index = 1;
        foreach ($params as $param) {
            $stmt->bindValue($index++, $param);
        }

        $stmt->bindValue($index++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($index++, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Đếm tổng số sản phẩm khi lọc */
    public function countFiltered($categories, $colors, $priceRange, string $keyword = '')
    {
        $sql = "SELECT COUNT(DISTINCT p.id) as total
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN inventory i ON p.id = i.product_id
                WHERE p.is_active = 1";
        $params = [];

        if (!empty($categories) && !in_array('all', $categories)) {
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $sql .= " AND p.category_id IN ($placeholders)";
            $params = array_merge($params, $categories);
        }

        if (!empty($colors) && !in_array('all', $colors)) {
            $placeholders = implode(',', array_fill(0, count($colors), '?'));
            $sql .= " AND p.color IN ($placeholders)";
            $params = array_merge($params, $colors);
        }


        if (!empty($priceRange)) {
            [$min, $max] = explode('-', $priceRange);
            $sql .= " AND p.price BETWEEN ? AND ?";
            $params[] = (int)$min;
            $params[] = (int)$max;
        }

        if (!empty($keyword)) {
            $sql .= " AND (p.name COLLATE utf8mb4_unicode_ci LIKE ? 
                        OR p.description COLLATE utf8mb4_unicode_ci LIKE ?)";
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    /** Phân trang mặc định (thêm category và tồn kho) */
    public function getPaginated($limit, $offset)
    {
        $sql = "SELECT p.*, pi.url, c.name AS category_name, i.stock,
                    COALESCE(ROUND(AVG(r.rating),1), 5) AS rating
                FROM products p
                LEFT JOIN product_images pi 
                    ON pi.product_id = p.id AND pi.is_primary = 1
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN inventory i ON p.id = i.product_id
                LEFT JOIN reviews r ON r.product_id = p.id AND r.is_approved = 1
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

    public function reduceStock($productId, $quantity)
    {
        $stmt = $this->conn->prepare("UPDATE inventory SET stock = GREATEST(stock - ?, 0) WHERE product_id = ?");
        $stmt->execute([$quantity, $productId]);
    }

    /** Lấy 4 sản phẩm nổi bật (rating cao nhất, nếu bằng thì mới nhất) */
    public function getTopRatedProducts($limit = 4)
    {
        $sql = "SELECT p.*, pi.url, c.name AS category_name, i.stock,
                    COALESCE(ROUND(AVG(r.rating), 1), 5) AS rating
                FROM products p
                LEFT JOIN product_images pi 
                    ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN inventory i ON p.id = i.product_id
                LEFT JOIN reviews r ON r.product_id = p.id AND r.is_approved = 1
                WHERE p.is_active = 1
                GROUP BY p.id
                ORDER BY rating DESC, p.created_at DESC
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Lấy 4 sản phẩm mới nhất */
    public function getNewestProducts($limit = 4)
    {
        $sql = "SELECT p.*, pi.url, c.name AS category_name, i.stock,
                    COALESCE(ROUND(AVG(r.rating), 1), 5) AS rating
                FROM products p
                LEFT JOIN product_images pi 
                    ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN inventory i ON p.id = i.product_id
                LEFT JOIN reviews r ON r.product_id = p.id AND r.is_approved = 1
                WHERE p.is_active = 1
                GROUP BY p.id
                ORDER BY p.created_at DESC, p.id DESC
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /** Đếm số lượng sản phẩm theo từng danh mục */
    public function countProductsByCategory()
    {
        $sql = "SELECT c.id AS category_id, c.name AS category_name, COUNT(p.id) AS total
                FROM categories c
                LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
                GROUP BY c.id, c.name
                ORDER BY c.id ASC";
        return $this->query($sql);
    }

}
