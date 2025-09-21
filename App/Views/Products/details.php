
<!-- Config -->
<?php include_once __DIR__ . '/../../Includes/config.php'; ?>

<!-- Head -->
<?php include_once __DIR__ . '/../../Includes/head.php'; ?>

<body>

<!-- Header -->
<?php include_once '../Layouts/Header.php'; ?>

<main class="product-detail-container">

  <!-- ========== Chi tiết sản phẩm ========== -->
  <section class="product-detail">
    <div class="product-gallery">
      <img class="main-image" src="<?= BASE_URL ?>App/Assets/Image/Products/flower1.png" alt="Khoảnh khắc ngọt ngào">
      <div class="thumbnail-list">
        <img src="<?= BASE_URL ?>App/Assets/Image/Products/flower1.png" alt="">
        <img src="<?= BASE_URL ?>App/Assets/Image/Products/flower1.png" alt="">
        <img src="<?= BASE_URL ?>App/Assets/Image/Products/flower1.png" alt="">
      </div>
    </div>

    <div class="product-info">
      <p class="category">Bó hoa</p>
      <h2 class="name">Khoảnh khắc ngọt ngào <span class="stock in">Còn hàng</span></h2>
      <div class="rating">
        ⭐ <strong>4.8</strong>
      </div>
      <p class="price">
        48.000đ <del>55.000đ</del>
      </p>

      <h4>Mô tả:</h4>
      <p class="description">
        Một sự kết hợp dịu dàng giữa hoa hồng phấn, mẫu đơn và lá xanh tươi — bó hoa “Khoảnh khắc ngọt ngào” hoàn hảo để thể hiện tình yêu, sự biết ơn hoặc đơn giản là để làm sáng bừng một ngày của ai đó.
      </p>
      <p class="description">
        Dù là một cử chỉ lãng mạn hay một bất ngờ ngọt ngào, bó hoa này mang đến sự ấm áp, thanh lịch và niềm vui trong từng bông hoa.
      </p>

      <label for="card-message"><strong>Lời nhắn trên thiệp</strong></label>
      <textarea id="card-message" placeholder="Nhập lời nhắn của bạn..."></textarea>

      <div class="actions">
        <div class="quantity">
          <button>-</button>
          <input id="input_quantity" type="number" value="1" min="1">
          <button>+</button>
        </div>
        <button class="btn add">Thêm vào giỏ</button>
        <button class="btn buy">Mua ngay</button>
        <button class="btn fav favorite">  <i class="fa-regular fa-heart"></i></button>
      </div>
    </div>
  </section>

  <!-- ========== Sản phẩm liên quan ========== -->
  <section class="related-products">
    <h3>Sản phẩm liên quan</h3>
    <p><strong>Khám phá <span class="highlight">những sản phẩm tương tự</span></strong></p>

    <div class="product-grid">
      <div class="product-card">
        <span class="tag">Giảm 30%</span>
        <img src="<?= BASE_URL ?>App/Assets/Image/Products/flower2.png" alt="Chạm yêu kiều">
        <h4>Chạm yêu kiều</h4>
        <p>32.000đ <del>38.000đ</del></p>
        <div class="rating">⭐ 4.8</div>
      </div>
      <!-- Thêm sản phẩm khác nếu cần -->
    </div>
  </section>
</main>

<!-- Footer -->
<?php include_once '../Layouts/Footer.php'; ?>

<!-- Script -->
<script src="<?= BASE_URL ?>App/Assets/Js/product-details.js"></script>
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
</body>