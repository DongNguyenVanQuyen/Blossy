<?php
require_once 'BaseModel.php';

class VoucherModel extends BaseModel
{
    /** Lấy thông tin voucher hợp lệ theo mã */
    public function getActiveVoucher(string $code): ?array
    {
        $sql = "SELECT *
                FROM vouchers
                WHERE code = ?
                  AND is_active = 1
                  AND NOW() BETWEEN starts_at AND ends_at
                LIMIT 1";

        $rows = $this->readitem($sql, [$code]);
        $voucher = $rows[0] ?? null;

        if (!$voucher) return null;

        // 🔹 Kiểm tra tổng lượt sử dụng
        $usageCount = $this->getUsageCount($voucher['id']);
        if ($usageCount >= $voucher['total_quantity']) {
            $voucher['error_message'] = 'Mã giảm giá đã hết lượt sử dụng!';
            return $voucher;
        }

        // 🔹 Kiểm tra giới hạn mỗi user
        if (!empty($voucher['per_user_limit']) && isset($_SESSION['user']['user_id'])) {
            $userUsage = $this->getUserUsageCount($voucher['id'], $_SESSION['user']['user_id']);
            if ($userUsage >= $voucher['per_user_limit']) {
                $voucher['error_message'] = 'Bạn đã sử dụng mã này quá số lần cho phép!';
                return $voucher;
            }
        }

        return $voucher;
    }

    public function calculateDiscount(array $voucher, float $subtotal): float
    {
        if (empty($voucher)) return 0.0;
        if (!empty($voucher['min_order_total']) && $subtotal < $voucher['min_order_total']) return 0.0;

        $discount = 0.0;
        if ($voucher['type'] === 'percent') {
            $discount = $subtotal * ($voucher['value'] / 100);
            if (!empty($voucher['max_discount'])) {
                $discount = min($discount, $voucher['max_discount']);
            }
        } elseif ($voucher['type'] === 'amount') {
            $discount = $voucher['value'];
        }

        return max(0.0, $discount);
    }

    public function recordUsage($voucherId, $userId, $orderId)
    {
        $sql = "INSERT INTO voucher_usages (voucher_id, user_id, order_id, used_at)
                VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$voucherId, $userId, $orderId]);
    }

    public function getUsageCount($voucherId): int
    {
        $sql = "SELECT COUNT(*) AS total FROM voucher_usages WHERE voucher_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$voucherId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    public function getUserUsageCount($voucherId, $userId): int
    {
        $sql = "SELECT COUNT(*) AS total FROM voucher_usages WHERE voucher_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$voucherId, $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }
}
