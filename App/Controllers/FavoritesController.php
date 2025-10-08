<?php
require_once __DIR__ . '/../Models/FavoritesModel.php';
require_once __DIR__ . '/BaseController.php';

class FavoritesController extends BaseController
{
    private $favoritesModel;

    public function __construct()
    {
        $this->favoritesModel = new FavoritesModel();
    }
    public function index()
    {
        global $title;
        $title = "Danh Sách Yêu Thích | Blossy";

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user']['user_id'] ?? null;

        if (!$userId) {
            // Nếu chưa đăng nhập → chuyển hướng đến trang đăng nhập
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Lấy danh sách sản phẩm yêu thích
        $favorites = $this->favoritesModel->getByUser($userId);

        // Gửi dữ liệu sang view
        $this->loadView('User.FavoritesList', [
            'favorites' => $favorites
        ]);
    }

    // Gọi AJAX toggle yêu thích
  public function toggle()
{
    if (ob_get_length()) ob_end_clean(); // xoá mọi output cũ
    header('Content-Type: application/json; charset=utf-8');

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $userId = $_SESSION['user']['user_id'] ?? null;
    $productId = $_POST['product_id'] ?? null;

    if (!$userId || !$productId) {
        echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập hoặc thiếu dữ liệu']);
        exit;
    }

    $isFavorite = $this->favoritesModel->isFavorite($userId, $productId);

    if ($isFavorite) {
        $this->favoritesModel->removeFavorite($userId, $productId);
        echo json_encode(['success' => true, 'favorited' => false]);
    } else {
        $this->favoritesModel->addFavorite($userId, $productId);
        echo json_encode(['success' => true, 'favorited' => true]);
    }

    exit;
}


}
