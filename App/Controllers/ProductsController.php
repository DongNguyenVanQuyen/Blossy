<?php
require_once 'BaseController.php';

class ProductsController extends BaseController
{
    private $productModel;

    public function __construct()
    {
    //    $this->loadModel('Product');
    //    $this->productModel = new ProductModel();
    }

    public function index()
    {
        global $title;
        $title = "Cửa hàng | Blossy";

 //       $products = $this->productModel->getAll();

        $this->loadView('Products.List');
    }
}
