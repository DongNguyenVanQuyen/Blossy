<?php
require_once 'BaseModel.php';

class VoucherModel extends BaseModel
{
    /**
     * Lấy voucher hợp lệ theo mã
     */
    public function getActiveVoucher($code)
    {
        $sql = "SELECT * FROM vouchers 
                WHERE code = ? 
                  AND is_active = 1
                  AND NOW() BETWEEN starts_at AND ends_at
                LIMIT 1";
        $rows = $this->readitem($sql, [$code]);
        return $rows[0] ?? null;
    }
}
