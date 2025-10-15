<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/AdminProductModel.php';
require_once __DIR__ . '/../Models/CategoryModel.php';

class AdminProductController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminProductModel();
    }

    /** Chặn truy cập không phải admin */
    private function guardAdmin()
    {
        if (!isset($_SESSION['user']) || (int)$_SESSION['user']['role_id'] !== 3) {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Bạn không có quyền truy cập Admin!'];
            header("Location: index.php");
            exit;
        }
    }

    /** Danh sách sản phẩm */
    public function index()
    {
        $this->guardAdmin();
        global $title;
        $title = "Quản lý sản phẩm | Blossy Admin";

        $limit = 15;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $total = $this->model->countAll();
        $totalPages = ceil($total / $limit);
        $products = $this->model->getPaginated($limit, $offset);

        $this->loadView('Admin.Products.List', compact('products', 'page', 'totalPages'));
    }

    /** Form thêm / sửa */
    public function edit($id = null)
    {
        $this->guardAdmin();
        $categoryModel = new CategoryModel();

        $product = $id ? $this->model->getById($id) : null;
        $categories = $categoryModel->getAllActive();
        $images = $id ? $this->model->getImagesByProduct($id) : [];

        $this->loadView('Admin.Products.Form', compact('product', 'categories', 'images'));
    }

    /** Lưu sản phẩm */
public function save()
{
    $this->guardAdmin();

    $data = [
        'category_id'       => $_POST['category_id'],
        'name'              => $_POST['name'],
        'color'             => $_POST['color'],
        'slug'              => $_POST['slug'],
        'season'            => $_POST['season'],
        'description'       => $_POST['description'],
        'price'             => $_POST['price'],
        'compare_at_price'  => $_POST['compare_at_price'],
        'is_active'         => isset($_POST['is_active']) ? 1 : 0
    ];

    // === CẬP NHẬT hoặc THÊM MỚI ===
    if (!empty($_POST['id'])) {
        $id = $_POST['id'];
        $data['slug'] = $this->model->ensureUniqueSlug($data['slug'], $id);
        $this->model->update($id, $data);
    } else {
        $data['slug'] = $this->model->ensureUniqueSlug($data['slug']);
        $id = $this->model->insert($data);
    }


    //XÓA ẢNH CŨ NẾU CÓ ẢNH MỚI UPLOAD
        $hasMain = !empty($_POST['main_url']);
        $hasSub  = !empty($_POST['sub_urls']) && is_array($_POST['sub_urls']) && count(array_filter($_POST['sub_urls'])) > 0;

        if (!empty($id)) {
            try {
                // Nếu có ảnh chính mới → xóa ảnh chính cũ
                if ($hasMain) {
                    $stmt = $this->model->conn->prepare("DELETE FROM product_images WHERE product_id = ? AND is_primary = 1");
                    $stmt->execute([$id]);
                    error_log("Đã xóa ảnh CHÍNH cũ của sản phẩm #$id");
                }

                // Nếu có ảnh phụ mới → xóa toàn bộ ảnh phụ cũ
                if ($hasSub) {
                    $stmt = $this->model->conn->prepare("DELETE FROM product_images WHERE product_id = ? AND is_primary = 0");
                    $stmt->execute([$id]);
                    error_log("Đã xóa ảnh PHỤ cũ của sản phẩm #$id");
                }
            } catch (PDOException $e) {
                error_log("Lỗi khi xóa ảnh cũ: " . $e->getMessage());
            }
        }


    // === LƯU ẢNH CHÍNH ===
    if (!empty($_POST['main_url'])) {
    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Lưu ảnh chính thành công!'];
        $this->model->saveImage($id, $_POST['main_url'], 1, 1);
    }

    // === LƯU ẢNH PHỤ ===
    if (!empty($_POST['sub_urls']) && is_array($_POST['sub_urls'])) {
        $sortOrder = 2;
        foreach ($_POST['sub_urls'] as $url) {
            if (!empty($url)) {
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Lưu ảnh phụ thành công!'];
                $this->model->saveImage($id, $url, 0, $sortOrder++);
            }
        }
    }

    // === TỒN KHO ===
    $this->model->saveInventory($id, $_POST['stock'] ?? 0, $_POST['threshold'] ?? 5);

    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Lưu sản phẩm thành công!'];
    header("Location: index.php?controller=adminProduct&action=index");
    exit;
}

    /** Xóa sản phẩm */
    public function delete($id)
    {
        $this->guardAdmin();
        $this->model->delete($id);
        $_SESSION['toast'] = ['type' => 'success', 'message' => '🗑️ Đã xóa sản phẩm!'];
        header("Location: index.php?controller=adminProduct&action=index");
        exit;
    }
}
