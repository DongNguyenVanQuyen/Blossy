<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/StaffModel.php';

class AdminStaffController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new StaffModel();
    }

    private function guardAdmin()
    {
        if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role_id'] !== 3) {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Không có quyền truy cập!'];
            header("Location: index.php");
            exit;
        }
    }

    /** 🟢 Hiển thị danh sách */
    public function index()
    {
        $this->guardAdmin();
        global $title;
        $title = "Quản lý Nhân Viên | Blossy";

        $staffs = $this->model->getAll();
        $this->loadView('Admin.Staff.List', ['staffs' => $staffs]);
    }

    /** ➕ Thêm nhân viên */
    public function create()
    {
        $this->guardAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create($_POST);
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Đã thêm nhân viên mới!'];
        }

        header("Location: index.php?controller=adminstaff&action=index");
        exit;
    }

    /** ✏️ Sửa nhân viên */
    public function edit()
    {
        $this->guardAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($_POST);
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Đã cập nhật nhân viên thành công!'];
        }

        header("Location: index.php?controller=adminstaff&action=index");
        exit;
    }

    /** 🗑️ Xóa nhân viên */
    public function delete($id)
    {
        $this->guardAdmin();
        $this->model->delete($id);
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Đã xóa nhân viên!'];
        header("Location: index.php?controller=adminstaff&action=index");
        exit;
    }

    /** 🔁 Khóa / mở khóa */
    public function toggle()
    {
        $this->guardAdmin();
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? null;
        if (!$id) { echo json_encode(['success' => false]); exit; }

        $this->model->toggleBlock($id);
        echo json_encode(['success' => true]);
        exit;
    }
}
