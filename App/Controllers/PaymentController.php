<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/PaymentModel.php';

class PaymentController extends BaseController
{
    private $paymentModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
    }

    /**
     * Lấy danh sách phương thức thanh toán của user (AJAX / API)
     */
    public function getMethods()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!isset($_SESSION['user']['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
            return;
        }

        $userId = $_SESSION['user']['user_id'];
        $methods = $this->paymentModel->getUserMethods($userId);
        echo json_encode(['success' => true, 'methods' => $methods]);
    }

    /**
     * Thêm phương thức thanh toán mới
     */
    public function add()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['user']['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập']);
            return;
        }

        $userId = $_SESSION['user']['user_id'];
        $brand = $_POST['card_brand'] ?? '';
        $holder = $_POST['card_holder'] ?? '';
        $last4 = $_POST['card_number_last4'] ?? '';
        $expiry = $_POST['expiry_date'] ?? '';

        if (!$brand || !$holder || !$last4 || !$expiry) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin thẻ']);
            return;
        }

        // Tránh thêm trùng
        if ($this->paymentModel->hasMethod($userId, $brand)) {
            echo json_encode(['success' => false, 'message' => 'Phương thức này đã tồn tại']);
            return;
        }

        $this->paymentModel->addMethod($userId, $brand, $holder, $last4, $expiry);
        echo json_encode(['success' => true, 'message' => 'Thêm phương thức thanh toán thành công!']);
    }
}
