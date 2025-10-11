<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/ProductModel.php';

class HomeController extends BaseController
{
    public function index()
    {
        global $title;
        $title = "Trang chủ | Blossy";

        $productModel = new ProductModel();

        $featuredProducts = $productModel->getTopRatedProducts(4);
        $newProducts = $productModel->getNewestProducts(4);
        $categories = $productModel->countProductsByCategory();

        $data = [
            'featuredProducts' => $featuredProducts,
            'newProducts' => $newProducts,
            'categoriesQuantity' => $categories
        ];

        $this->loadView('Home.index', $data);
    }
}
