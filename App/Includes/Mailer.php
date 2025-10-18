<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../Public/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../Public/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../Public/PHPMailer/src/SMTP.php';

/**
 * 📩 Gửi mã OTP
 */
function sendOTP($toEmail, $otp)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'vanquyen291104@gmail.com';
        $mail->Password = 'btdssrcocaxodfsy'; // App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('vanquyen291104@gmail.com', 'Blossy Shop');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = 'Mã xác thực OTP - Blossy';
        $mail->Body = "
            <h3>Xin chào,</h3>
            <p>Mã OTP để xác thực tài khoản của bạn là:</p>
            <div style='font-size:20px;font-weight:bold;color:#b4662a;'>{$otp}</div>
            <p>Mã có hiệu lực trong 5 phút. Vui lòng không chia sẻ cho người khác.</p>
            <br>
            <small>Trân trọng, <b>Blossy Team</b></small>
        ";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * 🧾 Gửi mail xác nhận đơn hàng
 */
function sendOrderConfirmation($toEmail, $orderData, $orderItems)
{
    $mail = new PHPMailer(true);
    try {
        // ========================
        // ⚙️ CẤU HÌNH SMTP
        // ========================
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'vanquyen291104@gmail.com';  // Email gửi
        $mail->Password = 'btdssrcocaxodfsy';          // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // ========================
        // 📨 NGƯỜI GỬI & NGƯỜI NHẬN
        // ========================
        $mail->setFrom('vanquyen291104@gmail.com', 'Blossy Shop');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = 'Xác nhận đơn hàng BLOSSY #' . htmlspecialchars($orderData['code']);

        // ========================
        // 🧾 DANH SÁCH SẢN PHẨM
        // ========================
        $itemsHtml = '';
        foreach ($orderItems as $item) {
            $lineTotal = ($item['price'] * $item['quantity']) - ($item['discount'] ?? 0);
            $discountHtml = !empty($item['discount']) && $item['discount'] > 0
                ? "<small style='color:#b4662a;'>(-" . number_format($item['discount'], 0, ',', '.') . "đ)</small>"
                : "";
            $itemsHtml .= "
            <tr style='border-bottom:1px solid #e7d4c2;'>
                <td style='padding:8px 6px;'>{$item['name']}</td>
                <td style='text-align:center;'>{$item['quantity']}</td>
                <td style='text-align:right;'>" . number_format($item['price'], 0, ',', '.') . "đ $discountHtml</td>
                <td style='text-align:right;'>" . number_format($lineTotal, 0, ',', '.') . "đ</td>
            </tr>";
        }

        // ========================
        // 💰 THÔNG TIN TỔNG KẾT
        // ========================
        $totalDiscount = 0;
        foreach ($orderItems as $it) {
            if (!empty($it['discount'])) {
                $totalDiscount += (float)$it['discount'];
            }
        }
        $discount = number_format($totalDiscount, 0, ',', '.') . 'đ';

        $grandTotal = number_format($orderData['grand_total'], 0, ',', '.') . 'đ';

        // ========================
        // 📧 NỘI DUNG EMAIL
        // ========================
        $mail->Body = "
        <div style='font-family:Arial,sans-serif;color:#4b2e23;background:#fff7ef;padding:20px;border-radius:10px;'>
            <h2 style='color:#b4662a;'>🌸 Cảm ơn bạn đã mua hàng tại BLOSSY!</h2>
            <p>Xin chào <strong>{$orderData['customer_name']}</strong>,</p>
            <p>Đơn hàng của bạn <strong>#{$orderData['code']}</strong> đã được tiếp nhận thành công.</p>

            <h3 style='margin-top:20px;'>Chi tiết đơn hàng:</h3>
            <table style='width:100%;border-collapse:collapse;font-size:15px;'>
                <thead>
                    <tr style='background:#f6e6d6;'>
                        <th style='text-align:left;padding:8px;'>Sản phẩm</th>
                        <th style='text-align:center;padding:8px;'>SL</th>
                        <th style='text-align:right;padding:8px;'>Giá</th>
                        <th style='text-align:right;padding:8px;'>Tổng</th>
                    </tr>
                </thead>
                <tbody   text-align: center;>$itemsHtml</tbody>
            </table>

            <div style='margin-top:20px;'>
                <p><strong>Giảm giá:</strong> <span style='color:#b4662a;'>-$discount</span></p>
                <p><strong>Phí vận chuyển:</strong> 30.000đ</p>
                <p><strong>Tổng cộng:</strong> <span style='font-size:18px;color:#b4662a;font-weight:bold;'>$grandTotal</span></p>
            </div>

            <hr style='margin:20px 0;border:0;border-top:1px solid #e7d4c2;'>
            <p><strong>Phương thức thanh toán:</strong> {$orderData['payment_method']}</p>
            <p><strong>Địa chỉ giao hàng:</strong> {$orderData['address']}</p>

            <hr style='margin:20px 0;border:0;border-top:1px solid #e7d4c2;'>
            <p style='font-size:14px;'>Nếu bạn cần hỗ trợ, vui lòng liên hệ email 
                <a href='mailto:support@blossy.vn' style='color:#b4662a;'>support@blossy.vn</a>.
            </p>
            <p style='font-weight:bold;color:#b4662a;'>💐 BLOSSY – Nơi gửi gắm những đóa hoa yêu thương!</p>
        </div>";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Lỗi gửi mail đơn hàng: {$mail->ErrorInfo}");
        return false;
    }
}

