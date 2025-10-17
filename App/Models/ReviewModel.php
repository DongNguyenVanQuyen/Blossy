<?php
require_once 'BaseModel.php';

class ReviewModel extends BaseModel
{
        public function getAllReviews($limit, $offset)
    {
        $sql = "SELECT r.*, p.name AS product_name, u.first_name, u.last_name
                FROM reviews r
                JOIN products p ON r.product_id = p.id
                JOIN users u ON r.user_id = u.id
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAllReviews()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM reviews");
        return (int)$stmt->fetchColumn();
    }

    public function updateVisibility($id, $isApproved)
    {
        $stmt = $this->conn->prepare("UPDATE reviews SET is_approved = ? WHERE id = ?");
        return $stmt->execute([$isApproved, $id]);
    }

    public function deleteReview($id)
    {
        // Xóa ảnh trước (nếu có)
        $this->conn->prepare("DELETE FROM review_images WHERE review_id = ?")->execute([$id]);
        $stmt = $this->conn->prepare("DELETE FROM reviews WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Lưu review chính
    public function addReview($data)
    {
        $sql = "INSERT INTO reviews (user_id, product_id, order_item_id, rating, title, content, is_approved, created_at)
                VALUES (:user_id, :product_id, :order_item_id, :rating, :title, :content, 1, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':product_id' => $data['product_id'],
            ':order_item_id' => $data['order_item_id'],
            ':rating' => $data['rating'],
            ':title' => $data['title'],
            ':content' => $data['content']
        ]);
        return $this->conn->lastInsertId();
    }

    // Lưu ảnh review
    public function addReviewImage($reviewId, $url)
    {
        $sql = "INSERT INTO review_images (review_id, image_url, created_at)
                VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$reviewId, $url]);
    }

    public function getProductReviews($productId, $limit = 5, $offset = 0)
    {
        $sql = "SELECT r.*, u.first_name, u.last_name,
                    GROUP_CONCAT(ri.image_url) AS images
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                LEFT JOIN review_images ri ON ri.review_id = r.id
                WHERE r.product_id = ? AND r.is_approved = 1
                GROUP BY r.id
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, (int)$productId, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countProductReviews($productId)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reviews WHERE product_id = ? AND is_approved = 1");
        $stmt->execute([$productId]);
        return (int) $stmt->fetchColumn();
    }
    public function getAverageRating($productId)
    {
        $stmt = $this->conn->prepare("
            SELECT ROUND(AVG(rating), 1) AS avg_rating 
            FROM reviews 
            WHERE product_id = ? AND is_approved = 1
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchColumn() ?: 0;
    }

}
