<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/NotificationModel.php';

class AdminNotificationController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new NotificationModel();
    }

    /** 🔒 Chỉ admin mới được truy cập */
    private function guardAdmin()
    {
        if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role_id'] === 1) {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Bạn không có quyền truy cập Admin!'];
            header("Location: index.php");
            exit;
        }
    }

    /** ✅ Trang danh sách thông báo (có phân trang) */
    public function index()
    {
        $this->guardAdmin();
        global $title;
        $title = "Quản lý Thông Báo | Blossy Admin";

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $notifications = $this->model->getAll($limit, $offset);
        $total = $this->model->countAll();
        $totalPages = ceil($total / $limit);

        $this->loadView('Admin.Notifications.Index', [
            'notifications' => $notifications,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    /** Gửi thông báo mới (system) đến toàn bộ khách hàng */
    public function create()
    {
        $this->guardAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');

        if ($title === '' || $body === '') {
            echo json_encode(['success' => false, 'message' => 'Thiếu tiêu đề hoặc nội dung']);
            return;
        }

        $createdBy = $_SESSION['user']['user_id'] ?? 0;
        $conn = $this->model->conn;

        try {
            $conn->beginTransaction();

            // ✅ Ghi vào bảng messages
            $stmt = $conn->prepare("
                INSERT INTO messages (title, body, type, created_by, created_at)
                VALUES (?, ?, 'system', ?, NOW())
            ");
            $stmt->execute([$title, $body, $createdBy]);
            $msgId = $conn->lastInsertId();

            // ✅ Gắn cho tất cả user role_id = 1
            $users = $conn->query("SELECT id FROM users WHERE role_id = 1")->fetchAll(PDO::FETCH_COLUMN);
            $stmt2 = $conn->prepare("INSERT INTO message_users (message_id, user_id, is_read) VALUES (?, ?, 0)");
            foreach ($users as $uid) {
                $stmt2->execute([$msgId, $uid]);
            }

            $conn->commit();
            echo json_encode(['success' => true]);
        } catch (Throwable $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
        public function save()
    {
        $this->guardAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');

        if ($title === '' || $body === '') {
            echo json_encode(['success' => false, 'message' => 'Thiếu tiêu đề hoặc nội dung']);
            return;
        }

        $conn = $this->model->conn;
        $createdBy = $_SESSION['user']['user_id'] ?? 0;

        try {
            if ($id > 0) {
                // Cập nhật
                $stmt = $conn->prepare("UPDATE messages SET title=?, body=?, updated_at=NOW() WHERE id=?");
                $stmt->execute([$title, $body, $id]);
                echo json_encode(['success' => true, 'updated' => true]);
            } else {
                // Thêm mới (system notice)
                $conn->beginTransaction();
                $stmt = $conn->prepare("
                    INSERT INTO messages (title, body, type, created_by, created_at)
                    VALUES (?, ?, 'system', ?, NOW())
                ");
                $stmt->execute([$title, $body, $createdBy]);
                $msgId = $conn->lastInsertId();

                $users = $conn->query("SELECT id FROM users WHERE role_id = 1")->fetchAll(PDO::FETCH_COLUMN);
                $stmt2 = $conn->prepare("INSERT INTO message_users (message_id, user_id, is_read) VALUES (?, ?, 0)");
                foreach ($users as $uid) {
                    $stmt2->execute([$msgId, $uid]);
                }

                $conn->commit();
                echo json_encode(['success' => true, 'created' => true]);
            }
        } catch (Throwable $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /** ❌ Xoá thông báo */
    public function delete()
    {
        $this->guardAdmin();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
            return;
        }

        try {
            $this->model->delete($id);
            echo json_encode(['success' => true]);
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /** 🔹 Lấy chi tiết 1 thông báo để sửa */
    public function getById()
    {
        $this->guardAdmin();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            echo json_encode(['success' => false]);
            return;
        }

        $data = $this->model->getById($id);
        echo json_encode(['success' => true, 'data' => $data]);
    }
}
