<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/VoucherModel.php';

class VoucherController extends BaseController
{
    public function apply()
    {
        header('Content-Type: application/json; charset=utf-8');

        $code = trim($_POST['code'] ?? '');
        $subtotal = (float)($_POST['subtotal'] ?? 0);

        if ($code === '') {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã voucher.']);
            exit;
        }

        $voucherModel = new VoucherModel();
        $voucher = $voucherModel->getActiveVoucher($code);

        if (!$voucher) {
            echo json_encode(['success' => false, 'message' => 'Mã không hợp lệ hoặc đã hết hạn.']);
            exit;
        }
        if (!empty($voucher['error_message'])) {
            echo json_encode(['success' => false, 'message' => $voucher['error_message']]);
            exit;
        }

        $discount = $voucherModel->calculateDiscount($voucher, $subtotal);
        if ($discount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Đơn hàng chưa đủ điều kiện áp dụng mã này.']);
            exit;
        }

        $newTotal = max(0, $subtotal - $discount);

        echo json_encode([
            'success' => true,
            'message' => "Đã áp dụng mã {$voucher['code']}",
            'code' => $voucher['code'],
            'discount' => number_format($discount, 0, ',', '.') . 'đ',
            'total' => number_format($newTotal, 0, ',', '.') . 'đ'
        ]);
        exit;
    }
}
