<?php
require_once 'BaseModel.php';

class AdminVoucherModel extends BaseModel
{
    /** Lấy danh sách voucher (phân trang) */
    public function getAll($limit, $offset)
    {
        $sql = "SELECT v.*, u.first_name, u.last_name
                FROM vouchers v
                LEFT JOIN users u ON v.created_by = u.id
                ORDER BY v.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Đếm tổng số voucher */
    public function countAll()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM vouchers");
        return (int)$stmt->fetchColumn();
    }

    /** Thêm voucher mới */
    public function create($data)
    {
        $sql = "INSERT INTO vouchers 
                (code, type, value, max_discount, min_order_total, total_quantity, per_user_limit, starts_at, ends_at, is_active, created_by)
                VALUES (:code, :type, :value, :max_discount, :min_order_total, :total_quantity, :per_user_limit, :starts_at, :ends_at, :is_active, :created_by)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    /** Cập nhật voucher */
    public function update($id, $data)
    {
        $sql = "UPDATE vouchers SET 
                    code=:code, type=:type, value=:value, max_discount=:max_discount,
                    min_order_total=:min_order_total, total_quantity=:total_quantity,
                    per_user_limit=:per_user_limit, starts_at=:starts_at, ends_at=:ends_at,
                    is_active=:is_active
                WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    /** Xóa voucher */
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM vouchers WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Bật / tắt voucher */
    public function toggle($id)
    {
        $sql = "UPDATE vouchers SET is_active = IF(is_active=1,0,1) WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    /** Lấy voucher theo ID */
    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM vouchers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
