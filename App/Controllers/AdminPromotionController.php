<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/AdminPromotionModel.php';
require_once __DIR__ . '/../Models/ProductModel.php';

class AdminPromotionController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminPromotionModel();
    }

    private function guardAdmin()
    {
        if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role_id'] !== 3) {
            $_SESSION['toast'] = ['type' => 'error', 'message' => '⚠️ Bạn không có quyền truy cập!'];
            header("Location: index.php");
            exit;
        }
    }

    public function index()
    {
        $this->guardAdmin();
        global $title;
        $title = "Quản lý khuyến mãi | Blossy";
        $promotions = $this->model->getAll();
        $this->loadView('Admin.Promotions.List', ['promotions' => $promotions]);
    }

    public function create()
    {
        $this->guardAdmin();
        global $title;
        $title = "Thêm khuyến mãi mới | Blossy";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create($_POST);
            $_SESSION['toast'] = ['type' => 'success', 'message' => '✅ Đã thêm khuyến mãi thành công!'];
            header("Location: index.php?controller=adminpromotion&action=index");
            exit;
        }

        $productModel = new ProductModel();
        $products = $productModel->getAll();
        $this->loadView('Admin.Promotions.Create', ['products' => $products]);
    }

      public function edit()
    {
        $this->guardAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($_POST);
            $_SESSION['toast'] = ['type' => 'success', 'message' => '✏️ Đã cập nhật khuyến mãi thành công!'];
        }

        header("Location: index.php?controller=adminpromotion&action=index");
        exit;
    }

    public function delete($id)
    {
        $this->guardAdmin();
        $this->model->delete($id);
        $_SESSION['toast'] = ['type' => 'success', 'message' => '🗑️ Đã xóa khuyến mãi!'];
        header("Location: index.php?controller=adminpromotion&action=index");
        exit;
    }

      public function toggle()
    {
        $this->guardAdmin();
        header('Content-Type: application/json; charset=utf-8');

        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false]);
            exit;
        }

        $this->model->toggleActive($id);
        echo json_encode(['success' => true]);
        exit;
    }
}
