<?php
require_once __DIR__ . '/BaseModel.php';

class FavoritesModel extends BaseModel
{
    public function isFavorite($userId, $productId): bool
    {
        $sql = "SELECT id FROM favorites WHERE user_id = ? AND product_id = ?";
        $stmt = $this->prepare($sql);
        $stmt->execute([$userId, $productId]);
        return $stmt->rowCount() > 0;
    }

    public function addFavorite($userId, $productId): bool
    {
        $sql = "INSERT INTO favorites (user_id, product_id, created_at) VALUES (?, ?, NOW())";
        $stmt = $this->prepare($sql);
        return $stmt->execute([$userId, $productId]);
    }


    public function removeFavorite($userId, $productId): bool
    {
        $sql = "DELETE FROM favorites WHERE user_id = ? AND product_id = ?";
        $stmt = $this->prepare($sql);
        return $stmt->execute([$userId, $productId]);
    }
    public function getByUser($userId): array
    {
        $sql = "
            SELECT 
                p.id,
                p.name,
                p.price,
                p.compare_at_price,
                p.color,
                p.slug,
                p.season,
                p.description,
                p.category_id,
                c.name AS category_name,
                i.stock,
                i.low_stock_threshold,
                img.url AS image_url,
                f.created_at
            FROM favorites f
            JOIN products p ON p.id = f.product_id
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN inventory i ON p.id = i.product_id
            LEFT JOIN product_images img 
                ON p.id = img.product_id 
                AND img.is_primary = 1
            WHERE f.user_id = ?
            GROUP BY p.id
            ORDER BY f.created_at DESC
        ";

        $stmt = $this->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




}
