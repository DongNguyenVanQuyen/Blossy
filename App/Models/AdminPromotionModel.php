<?php
require_once 'BaseModel.php';

class AdminPromotionModel extends BaseModel
{
    public function getAll()
    {
        $sql = "SELECT * FROM promotions ORDER BY starts_at DESC";
        return $this->query($sql);
    }
    public function create($data)
    {
        $sql = "INSERT INTO promotions (name, code, discount_percent, starts_at, ends_at, is_active, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['code'] ?? null,
            $data['discount_percent'],
            $data['starts_at'],
            $data['ends_at'],
            !empty($data['is_active']) ? 1 : 0,
            $_SESSION['user']['user_id'] ?? 1
        ]);
        return $this->conn->lastInsertId();
    }

        public function update($data)
    {
        $sql = "UPDATE promotions
                SET name = ?, code = ?, discount_percent = ?, starts_at = ?, ends_at = ?, is_active = ?
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['code'] ?? null,
            $data['discount_percent'],
            $data['starts_at'],
            $data['ends_at'],
            !empty($data['is_active']) ? 1 : 0,
            $data['id']
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM promotions WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function toggleActive($id)
    {
        $sql = "UPDATE promotions
                SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getActivePromotion()
    {
        $sql = "SELECT * FROM promotions
                WHERE is_active = 1
                AND NOW() BETWEEN starts_at AND ends_at
                ORDER BY starts_at DESC
                LIMIT 1";
        $rows = $this->readitem($sql);
        return $rows[0] ?? null;
    }
}
