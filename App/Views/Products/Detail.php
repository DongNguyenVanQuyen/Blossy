<?php
// Config & Head
include_once __DIR__ . '/../../Includes/config.php';
include_once __DIR__ . '/../../Includes/head.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Products/Products.css?v=<?= time() ?>">

<body>
<!-- Header -->
<?php include_once __DIR__ . '/../Layouts/Header.php'; ?>

<main class="product-detail-container">

  <!--  Chi tiết sản phẩm  -->
  <section class="product-detail">
    <div class="product-gallery">
      <img class="main-image" src="<?= $product['images'][0]['url'] ?? 'placeholder.jpg' ?>" alt="<?= htmlspecialchars($product['name']) ?>">
      <div class="thumbnail-list">
        <?php if (!empty($product['images'])): ?>
          <?php foreach ($product['images'] as $img): ?>
            <img src="<?= $img['url'] ?>" alt="Thumbnail">
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <div class="product-info">
      <p class="category">Loại: <?= $product['category_name'] ?? 'Đang cập nhật' ?></p>

      <h2 class="name">
        <?= htmlspecialchars($product['name']) ?>
        <span class="stock <?= ($product['stock'] ?? 0) > 0 ? 'in' : 'out' ?>">
          <?= ($product['stock'] ?? 0) > 0 ? 'Còn hàng' : 'Hết hàng' ?>
        </span>
      </h2>

      <div class="rating">⭐ <?= $product['rating'] ?? '5' ?></div>


      <p class="price">
        <?= number_format($product['price'], 0, ',', '.') ?>đ
        <?php if ($product['compare_at_price'] > $product['price']): ?>
          <del><?= number_format($product['compare_at_price'], 0, ',', '.') ?>đ</del>
        <?php endif; ?>
      </p>

      <h4>Mô tả:</h4>
      <p class="description">
        <?= nl2br(htmlspecialchars($product['description'])) ?>
      </p>  
      <p class="quantity">Số lượng: <?= $product['stock'] ?? 0 ?></p>

      <label for="card-message"><strong>Lời nhắn trên thiệp</strong></label>
      <textarea id="card-message" placeholder="Nhập lời nhắn của bạn..."></textarea>

      <div class="actions">
        <div class="quantity">
          <button class="btn-decrease">-</button>
          <input id="input_quantity" type="number" 
                value="1" min="1" 
                max="<?= $product['stock'] ?? 1 ?>" 
                data-stock="<?= $product['stock'] ?? 0 ?>">
          <button class="btn-increase">+</button>
        </div>
                
        <button class="btn add UserBtn add-to-cart" 
                data-id="<?= $product['id'] ?>"
                <?= ($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>
          Thêm vào giỏ
        </button>

        <button class="btn buy UserBtn checkout-now" 
                data-id="<?= $product['id'] ?>"
                <?= ($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>
          Mua ngay
        </button>

        
        <button type="button" class="favorite-btn" data-product-id="<?= $product['id'] ?>">
          <i class="fa fa-heart <?= !empty($product['is_favorite']) ? 'active' : '' ?>"></i>
        </button>

      </div>

    </div>
  </section>
  
    <!-- 💬 PHẦN ĐÁNH GIÁ -->
    <section class="product-reviews">
      <h3>Đánh giá sản phẩm</h3>

      <div class="review-summary">
        <div class="average-rating">
          ⭐ <strong><?= $averageRating ?: '5.0' ?></strong> / 5
        </div>
        <p>(<?= $totalReviews ?> đánh giá)</p>
      </div>

      <?php if (!empty($reviews)): ?>
        <div class="review-list" id="reviewList">
          <?php foreach ($reviews as $r): ?>
            <div class="review-item">
              <div class="review-header">
                <span class="review-author">
                  <?= htmlspecialchars(trim($r['first_name'] . ' ' . $r['last_name'])) ?>
                </span>
                <span class="review-stars">⭐ <?= intval($r['rating']) ?></span>
              </div>
              <?php if (!empty($r['title'])): ?>
                <p class="review-title"><strong><?= htmlspecialchars($r['title']) ?></strong></p>
              <?php endif; ?>
              <p class="review-content"><?= nl2br(htmlspecialchars($r['content'])) ?></p>
              <?php if (!empty($r['images'])): ?>
                <div class="review-images">
                  <?php foreach (explode(',', $r['images']) as $img): ?>
                    <img src="<?= htmlspecialchars($img) ?>" alt="Review Image">
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <span class="review-date"><?= date('d/m/Y', strtotime($r['created_at'])) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="no-reviews">🌸 Chưa có đánh giá nào cho sản phẩm này.</p>
      <?php endif; ?>

      <?php if ($totalPages > 1): ?>
        <div class="pagination reviews-pagination">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?controller=products&action=detail&id=<?= $product['id'] ?>&rpage=<?= $i ?>"
              class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </section>


  <!--  Sản phẩm liên quan  -->
  <section class="related-products">
    <h3>Sản phẩm liên quan</h3>
    <p><strong>Khám phá <span class="highlight">những sản phẩm tương tự</span></strong></p>
    
    <div class="product-grid">
      <?php foreach ($relatedProducts as $rel): ?>
        <a href="<?= BASE_URL ?>index.php?controller=products&action=detail&id=<?= $rel['id'] ?>" class="product-card">
          <?php if ($rel['compare_at_price'] > $rel['price']): ?>
            <span class="tag">
              Giảm <?= round((($rel['compare_at_price'] - $rel['price']) / $rel['compare_at_price']) * 100) ?>%
            </span>
          <?php endif; ?>

          <img src="<?= $rel['url'] ?>" alt="<?= htmlspecialchars($rel['name']) ?>">
          <h4><?= htmlspecialchars($rel['name']) ?></h4>
          <p>
            <?= number_format($rel['price'], 0, ',', '.') ?>đ
            <?php if ($rel['compare_at_price'] > $rel['price']): ?>
              <del><?= number_format($rel['compare_at_price'], 0, ',', '.') ?>đ</del>
            <?php endif; ?>
          </p>
          <div class="rating">⭐ 4.8</div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

</main>

<!-- Footer & Script -->
<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>
<script src="<?= BASE_URL ?>Public/Assets/Js/product-details.js?v=<?= time() ?>"></script>
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
</body>

<script src="<?= BASE_URL ?>Public/Assets/Js/LoadPayment.js?v=<?= time() ?>"></script>