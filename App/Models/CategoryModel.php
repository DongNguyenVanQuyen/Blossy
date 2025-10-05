<?php
require_once __DIR__ . '/BaseModel.php';

class CategoryModel extends BaseModel
{
    protected $table = 'categories';

    // Lấy tất cả loại hoa đang hoạt động
    public function getAllActive()
    {
        $sql = "SELECT id, name FROM {$this->table} WHERE is_active = 1 ORDER BY id ASC";
        return $this->query($sql);
    }

    // Lấy tên loại hoa theo ID (nếu cần)
    public function getNameById($id)
    {
        $sql = "SELECT name FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchColumn(); // Trả về tên
    }
}
