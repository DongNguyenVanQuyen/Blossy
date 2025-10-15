<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/NotificationModel.php';

class NotificationController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new NotificationModel();
    }

    /** 🔹 Trang danh sách thông báo (dạng chi tiết nếu cần) */
    public function index()
    {
        global $title;
        $title = "Thông Báo | Blossy";

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $userId = $_SESSION['user']['user_id'];
        $notifications = $this->model->getUserNotifications($userId);

        $this->loadView('Notification.Index', ['notifications' => $notifications]);
    }

    /** 🔹 Khi user click vào một thông báo trong panel */
    public function open()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $userId = $_SESSION['user']['user_id'];
        $messageId = $_GET['id'] ?? 0;

        $this->model->markAsRead($userId, $messageId);
        $msg = $this->model->getMessageById($messageId);

        if (!$msg) {
            echo "<script>alert('❌ Thông báo không tồn tại!');history.back();</script>";
            exit;
        }

        // 🔸 Nếu là thông báo đơn hàng → kiểm tra quyền truy cập
        if ($msg['type'] === 'order' && $msg['order_id']) {
            $check = $this->model->conn->prepare(
                "SELECT id FROM orders WHERE id = ? AND user_id = ?"
            );
            $check->execute([$msg['order_id'], $userId]);
            $valid = $check->fetch(PDO::FETCH_ASSOC);

            if (!$valid) {
                echo "<script>alert('⚠️ Bạn không có quyền xem đơn hàng này!');history.back();</script>";
                exit;
            }

            // ✅ Chuyển đến trang chi tiết đơn hàng
            header("Location: index.php?controller=order&action=detail&id=" . $msg['order_id']);
            exit;
        }

        // 🔹 Nếu là thông báo hệ thống/khuyến mãi → mở trang riêng
        header("Location: index.php?controller=notification&action=detail&id=" . $messageId);
        exit;
    }

    /** 🔹 Xem chi tiết nội dung thông báo hệ thống hoặc khuyến mãi */
    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        $msg = $this->model->getMessageById($id);

        if (!$msg) {
            echo "<script>alert('❌ Không tìm thấy thông báo!');history.back();</script>";
            exit;
        }

        global $title;
        $title = $msg['title'] ?? "Chi tiết thông báo";

        $this->loadView('Notification.Detail', ['message' => $msg]);
    }

    /**  Khi user click vào icon chuông */
    public function markAll()
    {
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $userId = $_SESSION['user']['user_id'];
        $this->model->markAllAsRead($userId);

        echo json_encode(['success' => true]);
        exit;
    }

}
