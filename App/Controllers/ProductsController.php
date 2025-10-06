<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Models/CategoryModel.php';

class ProductsController extends BaseController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $categoriesModel = new CategoryModel();
        $categories = $categoriesModel->getAllActive();

        $limit = 18;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $selectedCategories = isset($_GET['category']) ? (array)$_GET['category'] : ['all'];
        $selectedColors = isset($_GET['color']) ? (array)$_GET['color'] : [];
        $priceRange = isset($_GET['price_range']) ? $_GET['price_range'] : '';

        if (!empty($selectedCategories) || !empty($selectedColors) || !empty($priceRange)) {
            $products = $this->productModel->getFiltered($selectedCategories, $selectedColors, $priceRange, $limit, $offset);
            $totalProducts = $this->productModel->countFiltered($selectedCategories, $selectedColors, $priceRange);
        } else {
            $products = $this->productModel->getPaginated($limit, $offset);
            $totalProducts = $this->productModel->countAll();
        }

        $totalPages = ceil($totalProducts / $limit);

        $this->loadView('Products.List', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategories' => $selectedCategories,
            'selectedColors' => $selectedColors,
            'priceRange' => $priceRange,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    // Trả về CHỈ danh sách thẻ sản phẩm (không bọc .product-grid)
 public function filter()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Invalid request']); exit;
    }

    // Tắt hiển thị warning ra body (log vào error_log)
    ini_set('display_errors', '0');

    try {
        $page  = max(1, (int)($_POST['page'] ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $selectedCategories = $_POST['category'] ?? ['all']; // name="category[]"
        $selectedColors     = $_POST['color'] ?? [];         // name="color[]"
        $priceRange         = $_POST['price_range'] ?? '';

        $products      = $this->productModel->getFiltered($selectedCategories, $selectedColors, $priceRange, $limit, $offset);
        $totalProducts = $this->productModel->countFiltered($selectedCategories, $selectedColors, $priceRange);
        $totalPages    = max(1, (int)ceil($totalProducts / $limit));

        // Render partials THÀNH CHUỖI (không include layout, header, footer)
        ob_start();
        $productsVar = $products;           // tránh đè tên
        $products = $productsVar;
        include __DIR__ . '/../Views/Products/_ProductList.php';
        $productsHtml = ob_get_clean();

        ob_start();
        $pageVar = $page; $totalPagesVar = $totalPages;
        $page = $pageVar; $totalPages = $totalPagesVar;
        include __DIR__ . '/../Views/Products/_Pagination.php';
        $paginationHtml = ob_get_clean();

        // Xóa MỌI buffer đã có (phòng tránh BOM/echo trước đó)
        while (ob_get_level() > 0) { ob_end_clean(); }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'productsHtml'   => $productsHtml,
            'paginationHtml' => $paginationHtml,
            'totalProducts'  => $totalProducts,
            'currentPage'    => $page,
        ], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}




    public function detail()
    {
        global $title;
        $title = "Chi Tiết Sản Phẩm | Blossy";

        $id = $_GET['id'] ?? 0;
        $product = $this->productModel->getById($id);

        if (!$product) {
            echo "Sản phẩm không tồn tại!";
            return;
        }

        $relatedProducts = $this->productModel->getRelatedProducts($product['id'], $product['category_id']);

        $this->loadView('Products.Detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }
}
