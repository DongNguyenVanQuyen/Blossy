<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/AdminVoucherModel.php';

class AdminVoucherController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminVoucherModel();
    }

    private function guardAdmin()
    {
        if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role_id'] !== 3) {
            $_SESSION['toast'] = ['type' => 'error', 'message' => '⚠️ Bạn không có quyền truy cập Admin!'];
            header("Location: index.php");
            exit;
        }
    }

    /** Danh sách voucher */
    public function index()
    {
        $this->guardAdmin();
        global $title;
        $title = "Quản lý Voucher | Blossy Admin";

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $vouchers = $this->model->getAll($limit, $offset);
        $total = $this->model->countAll();
        $totalPages = ceil($total / $limit);

        $this->loadView('Admin.Voucher.Index', [
            'vouchers' => $vouchers,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    /** Thêm voucher */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code'            => $_POST['code'],
                'type'            => $_POST['type'],
                'value'           => $_POST['value'],
                'max_discount'    => $_POST['max_discount'] ?: null,
                'min_order_total' => $_POST['min_order_total'] ?: 0,
                'total_quantity'  => $_POST['total_quantity'] ?: 0,
                'per_user_limit'  => $_POST['per_user_limit'] ?: 1,
                'starts_at'       => $_POST['starts_at'],
                'ends_at'         => $_POST['ends_at'],
                'is_active'       => isset($_POST['is_active']) ? 1 : 0,
                'created_by'      => $_SESSION['user']['user_id']
            ];
            $this->model->create($data);
            $_SESSION['toast'] = ['type' => 'success', 'message' => '✅ Thêm voucher thành công!'];
            header("Location: index.php?controller=adminvoucher&action=index");
            exit;
        }
    }

    /** Cập nhật voucher */
    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = [
                'code'            => $_POST['code'],
                'type'            => $_POST['type'],
                'value'           => $_POST['value'],
                'max_discount'    => $_POST['max_discount'] ?: null,
                'min_order_total' => $_POST['min_order_total'] ?: 0,
                'total_quantity'  => $_POST['total_quantity'] ?: 0,
                'per_user_limit'  => $_POST['per_user_limit'] ?: 1,
                'starts_at'       => $_POST['starts_at'],
                'ends_at'         => $_POST['ends_at'],
                'is_active'       => isset($_POST['is_active']) ? 1 : 0
            ];
            $this->model->update($id, $data);
            $_SESSION['toast'] = ['type' => 'success', 'message' => '✅ Cập nhật voucher thành công!'];
            header("Location: index.php?controller=adminvoucher&action=index");
            exit;
        }
    }

    /** Xóa */
    public function delete()
    {
        if (isset($_GET['id'])) {
            $this->model->delete($_GET['id']);
            $_SESSION['toast'] = ['type' => 'success', 'message' => '🗑️ Xóa voucher thành công!'];
            header("Location: index.php?controller=adminvoucher&action=index");
            exit;
        }
    }

    /** Bật / tắt */
    public function toggle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $this->model->toggle($id);
            echo json_encode(['success' => true]);
        }
    }
}
