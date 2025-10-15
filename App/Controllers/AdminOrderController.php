<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/AdminOrderModel.php';

class AdminOrderController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminOrderModel();
    }

    /** Kiểm tra quyền admin */
    private function guardAdmin()
    {
        if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role_id'] === 1) {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Bạn không có quyền truy cập Admin!'];
            header("Location: index.php");
            exit;
        }
    }

    /** Trang danh sách đơn hàng */
    public function index()
    {
        $this->guardAdmin();
        global $title;
        $title = "Quản lý đơn hàng | Blossy Admin";

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $orders = $this->model->getAll($limit, $offset);
        $totalOrders = $this->model->countAll();
        $totalPages = ceil($totalOrders / $limit);

        $this->loadView('Admin.Orders.Index', compact('orders', 'page', 'totalPages'));
    }

    /**  Cập nhật trạng thái đơn hàng + gửi thông báo */
    public function updateStatus()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = strtolower(trim($_POST['status'] ?? ''));

        $allowed = ['cho_xac_nhan', 'dang_giao', 'hoan_thanh', 'huy'];
        if ($id <= 0 || !in_array($status, $allowed, true)) {
            echo json_encode(['success' => false, 'message' => 'Trạng thái không hợp lệ']);
            return;
        }

        // Lấy thông tin đơn hàng
        $order = $this->model->getById($id);
        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
            return;
        }

        $userId = (int)($order['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Đơn hàng không có người dùng hợp lệ']);
            return;
        }

        $oldStatus = strtolower(trim($order['status'] ?? ''));
        $justChanged = $oldStatus !== $status;

        try {
            $conn = $this->model->conn;
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Chỉ transaction phần update trạng thái (tránh lock quá nhiều)
            $conn->beginTransaction();
            $ok = $this->model->updateStatus($id, $status);
            $conn->commit();

            if (!$ok && $justChanged) {
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái đơn hàng']);
                return;
            }

            // Nếu hoàn thành thì cập nhật cấp độ khách hàng
            if ($status === 'hoan_thanh') {
                require_once __DIR__ . '/../Models/AdminCustomerModel.php';
                $custModel = new AdminCustomerModel();
                $custModel->updateTotalSpent();
                $custModel->updateCustomerLevels();
            }

            // Ghi thông báo (không cần transaction)
            $title = "Đơn hàng #{$id}";
            $body = match ($status) {
                'cho_xac_nhan' => "Đơn hàng #{$id} đang chờ xác nhận.",
                'dang_giao'    => "Đơn hàng #{$id} đang được giao đến bạn.",
                'hoan_thanh'   => "Đơn hàng #{$id} đã giao thành công! Cảm ơn bạn đã mua sắm tại Blossy.",
                'huy'          => "Đơn hàng #{$id} đã bị hủy. Nếu cần hỗ trợ, vui lòng liên hệ Blossy.",
                default        => "Đơn hàng #{$id} đã được cập nhật trạng thái."
            };

            $createdBy = $_SESSION['user']['user_id'] ?? 0;

            // Ghi vào bảng messages
            $stmtMsg = $conn->prepare("
                INSERT INTO messages (title, body, type, order_id, created_by, created_at)
                VALUES (?, ?, 'order', ?, ?, NOW())
            ");
            $stmtMsg->execute([$title, $body, $id, $createdBy]);
            $messageId = (int)$conn->lastInsertId();

            // Liên kết với người nhận
            $stmtLink = $conn->prepare("
                INSERT INTO message_users (message_id, user_id, is_read)
                VALUES (?, ?, 0)
            ");
            $stmtLink->execute([$messageId, $userId]);

            // Gửi phản hồi cho JS
            $displayText = match ($status) {
                'cho_xac_nhan' => 'Chờ xác nhận',
                'dang_giao'    => 'Đang giao',
                'hoan_thanh'   => 'Hoàn thành',
                'huy'          => 'Đã hủy',
                default        => 'Chưa xác định',
            };

            $displayClass = match ($status) {
                'cho_xac_nhan' => 'pending',
                'dang_giao'    => 'shipping',
                'hoan_thanh'   => 'success',
                'huy'          => 'cancel',
                default        => 'unknown',
            };

            echo json_encode([
                'success' => true,
                'displayText' => $displayText,
                'displayClass' => $displayClass,
                'notified' => true
            ]);
        } catch (Throwable $e) {
            try {
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
            } catch (Throwable $ignore) {}

            error_log("updateStatus error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ]);
        }
    }

    /** 🔹 Xem chi tiết đơn hàng */
    public function detail()
    {
        $this->guardAdmin();
        $id = $_GET['id'] ?? 0;

        $order = $this->model->getById($id);
        $items = $this->model->getOrderItems($id);

        if (!$order) {
            echo "<script>alert('Không tìm thấy đơn hàng!'); 
                  window.location='index.php?controller=adminorder&action=index';</script>";
            exit;
        }

        $this->loadView('Admin.Orders.Detail', compact('order', 'items'));
    }

}
