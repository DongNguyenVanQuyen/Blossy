<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/HeaderCountModel.php';

class HeaderCountController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new HeaderCountModel();
    }

    /** Lấy số lượng cho header (trả JSON nếu dùng AJAX) */
    public function getCounts()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'favorites' => 0, 'cart' => 0]);
            return;
        }

        $userId = $_SESSION['user']['user_id'];
        $favorites = $this->model->getFavoriteCount($userId);
        $cart = $this->model->getCartCount($userId);

        echo json_encode([
            'success' => true,
            'favorites' => $favorites,
            'cart' => $cart
        ]);
    }
}
