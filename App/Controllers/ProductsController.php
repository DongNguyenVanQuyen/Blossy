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

        $limit = 15;
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

    // ✅ Hàm xử lý filter trả về HTML cho AJAX
    public function filter()
    {
        $categories = $_POST['category'] ?? [];
        $colors = $_POST['color'] ?? [];
        $priceRange = $_POST['price_range'] ?? '';
        $limit = 24;
        $offset = 0;

        $products = $this->productModel->getFiltered($categories, $colors, $priceRange, $limit, $offset);

        ob_start();
        foreach ($products as $product) {
            include __DIR__ . '/../Views/Products/_ProductCard.php';
        }
        $html = ob_get_clean();

        echo $html; // ✅ Trả HTML về cho JS
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
