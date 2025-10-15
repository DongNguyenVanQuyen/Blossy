<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/AdminCustomerModel.php';

class AdminCustomerController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminCustomerModel();
    }

    private function guardAdmin()
    {
        if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role_id'] === 1) {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Bạn không có quyền truy cập Admin!'];
            header("Location: index.php");
            exit;
        }
    }

    /** Danh sách khách hàng */
    public function index()
    {
        $this->guardAdmin();
        global $title;
        $title = "Quản lý khách hàng | Blossy Admin";

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $customers = $this->model->getAll($limit, $offset);
        $total = $this->model->countAll();
        $totalPages = ceil($total / $limit);

        $this->loadView('Admin.Customer.Index', [
            'customers' => $customers,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    /** Khóa / Mở khóa tài khoản */
    public function toggle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $ok = $this->model->toggleStatus($id);
            echo json_encode(['success' => $ok,'message' => 'Cập Nhật Trạng Thái Thành Công!']);
        }
    }
}
