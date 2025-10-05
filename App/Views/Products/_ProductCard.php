<?php
$imageUrl = !empty($product['url'])
  ? htmlspecialchars($product['url'])
  : BASE_URL . "Public/Assets/Image/no-image.png"; // fallback nếu không có ảnh

$name = htmlspecialchars($product['name']);
$price = number_format($product['price'], 0, ',', '.') . "đ";
$compare = $product['compare_at_price']
  ? number_format($product['compare_at_price'], 0, ',', '.') . "đ"
  : '';
$discount = $product['compare_at_price']
  ? round((1 - ($product['price'] / $product['compare_at_price'])) * 100)
  : 0;
?>

<div class="product-card">
  <?php if ($discount > 0): ?>
    <span class="tag-off">Giảm <?= $discount ?>%</span>
  <?php endif; ?>

  <a href="<?= BASE_URL ?>index.php?controller=products&action=detail&id=<?= $product['id'] ?>">
    <img src="<?= $imageUrl ?>" alt="<?= $name ?>">
  </a>

  <h3><?= $name ?></h3>
  <p class="price">
    <?= $price ?>
    <?php if ($compare): ?>
      <del><?= $compare ?></del>
    <?php endif; ?>
  </p>
  <div class="rating">⭐ 4.8</div>
</div>
