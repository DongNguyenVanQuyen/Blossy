<?php
include_once __DIR__ . '/../../Includes/config.php';
include_once __DIR__ . '/../../Includes/head.php';
include_once __DIR__ . '/../Layouts/Header.php';

$product = $product ?? [];
$orderItemId = $order_item_id ?? 0;
?>

<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/User/ReviewForm.css?v=<?= time() ?>">

<div class="review-form">
  <h2>Đánh giá sản phẩm</h2>

  <div class="review-form__product">
    <img src="<?= htmlspecialchars($product['images'][0]['url'] ?? BASE_URL . 'Public/Assets/Image/no_image.png') ?>" 
         alt="<?= htmlspecialchars($product['name'] ?? 'Sản phẩm') ?>">
    <div>
      <h3><?= htmlspecialchars($product['name'] ?? 'Tên sản phẩm') ?></h3>
      <p>Mã đơn hàng: <?= htmlspecialchars($orderItemId) ?></p>
    </div>
  </div>

  <form action="index.php?controller=review&action=submit" method="POST" enctype="multipart/form-data" class="review-form__main">
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id'] ?? 0) ?>">
    <input type="hidden" name="order_item_id" value="<?= htmlspecialchars($orderItemId) ?>">

    <label>Tiêu đề:</label>
    <input type="text" name="title" placeholder="Tóm tắt ngắn gọn về đánh giá của bạn" required>

    <label>Đánh giá sao:</label>
    <!-- Font Awesome Stars -->
    <div class="stars">
    <?php for ($i = 5; $i >= 1; $i--): ?>
        <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
        <label for="star<?= $i ?>"><i class="fa-solid fa-star"></i></label>
    <?php endfor; ?>
    </div>


    <label>Nội dung:</label>
    <textarea name="content" rows="5" placeholder="Hãy chia sẻ cảm nhận của bạn..." required></textarea>

    <label>Thêm hình ảnh (tuỳ chọn):</label>
    <input type="file" name="images[]" multiple accept="image/*">

    <button type="submit" class="btn-submit">Gửi đánh giá</button>
  </form>
</div>

<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
