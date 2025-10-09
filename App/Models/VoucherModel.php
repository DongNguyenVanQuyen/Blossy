<?php
require_once 'BaseModel.php';

class VoucherModel extends BaseModel
{
    /**
     * Lấy thông tin voucher hợp lệ theo mã
     */
    public function getActiveVoucher(string $code): ?array
    {
        $sql = "SELECT *
                FROM vouchers
                WHERE code = ?
                  AND is_active = 1
                  AND NOW() BETWEEN starts_at AND ends_at
                LIMIT 1";

        $rows = $this->readitem($sql, [$code]);
        return $rows[0] ?? null;
    }

    /**
     * Tính toán giảm giá (áp dụng cho tổng đơn)
     * @param array $voucher  Dữ liệu voucher
     * @param float $subtotal  Tổng đơn hàng
     * @return float  Số tiền giảm giá
     */
    public function calculateDiscount(array $voucher, float $subtotal): float
    {
        if (empty($voucher)) return 0.0;

        // Nếu đơn hàng chưa đủ giá trị tối thiểu
        if (!empty($voucher['min_order_total']) && $subtotal < $voucher['min_order_total']) {
            return 0.0;
        }

        $discount = 0.0;

        // Nếu là giảm theo %
        if ($voucher['type'] === 'percent') {
            $discount = $subtotal * ($voucher['value'] / 100);
            if (!empty($voucher['max_discount'])) {
                $discount = min($discount, $voucher['max_discount']);
            }
        } 
        // Nếu là giảm theo số tiền cố định
        elseif ($voucher['type'] === 'fixed') {
            $discount = $voucher['value'];
        }

        return max(0.0, $discount);
    }
}
