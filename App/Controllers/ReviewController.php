<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../Models/ReviewModel.php';
require_once __DIR__ . '/../Includes/Cloudinary_config.php';

class ReviewController extends BaseController
{
    /**
     * Gửi đánh giá sản phẩm
     */
    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Phương thức không hợp lệ!'
        ];
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
        }

        if (!isset($_SESSION['user']['user_id'])) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Vui lòng đăng nhập để gửi đánh giá!'
            ];
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $userId = $_SESSION['user']['user_id'];
        $productId = $_POST['product_id'] ?? null;
        $orderItemId = $_POST['order_item_id'] ?? null;
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $rating = (int)($_POST['rating'] ?? 0);

        if (!$productId || !$orderItemId || !$title || !$content || $rating <= 0) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Vui lòng điền đầy đủ thông tin đánh giá!'
            ];
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }

        require_once __DIR__ . '/../Models/OrderModel.php';
        $orderModel = new OrderModel();

        $hasBought = $orderModel->hasUserBoughtProduct($userId, $productId);

        if (!$hasBought) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Bạn chưa mua sản phẩm này, không thể đánh giá!'
            ];
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }


        // Thêm review vào DB
        $reviewModel = new ReviewModel();
        $reviewId = $reviewModel->addReview([
            'user_id' => $userId,
            'product_id' => $productId,
            'order_item_id' => $orderItemId,
            'title' => $title,
            'content' => $content,
            'rating' => $rating,
        ]);

        // Nếu có upload ảnh
        if (!empty($_FILES['images']['tmp_name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $url = uploadToCloudinary($tmpName, 'webbanhoa/reviews');
                    if ($url) {
                        $reviewModel->addReviewImage($reviewId, $url);
                    }
                }
            }
        }

         $_SESSION['toast'] = [
            'type' => 'success',
            'message' => 'Cảm ơn bạn đã gửi đánh giá!'
        ];
        header("Location: index.php?controller=auth&action=info");
        exit;

    }
}
