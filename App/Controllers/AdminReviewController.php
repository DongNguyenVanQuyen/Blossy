<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/ReviewModel.php';

class AdminReviewController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new ReviewModel();
    }

    /** Kiểm tra quyền Admin */
    private function guardAdmin()
    {
        if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role_id'] === 1) {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Bạn không có quyền truy cập Admin!'];
            header("Location: index.php");
            exit;
        }
    }

    /** Hiển thị danh sách đánh giá */
    public function index()
    {
        $this->guardAdmin();
        global $title;
        $title = "Quản lý đánh giá | Blossy Admin";

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $reviews = $this->model->getAllReviews($limit, $offset);
        $totalReviews = $this->model->countAllReviews();
        $totalPages = ceil($totalReviews / $limit);

        $this->loadView('Admin.Reviews.index', [
            'reviews' => $reviews,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    /** Ẩn / hiển thị review */
    public function toggleVisibility()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $isApproved = (int)($_POST['is_approved'] ?? 0);

        $ok = $this->model->updateVisibility($id, $isApproved);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Cập nhật trạng thái thành công' : 'Không thể cập nhật'
        ]);
    }

    /** Xóa review */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $ok = $this->model->deleteReview($id);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Đã xóa đánh giá!' : 'Không thể xóa!'
        ]);
    }
}
