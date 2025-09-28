<?php
// Head
include_once __DIR__ . '/../../Includes/config.php';
include_once __DIR__ . '/../../Includes/head.php';
include_once __DIR__ . '/../Layouts/Header.php'; 
?>

<body>
  <header class="shop-header">
    <h1>Cửa Hàng</h1>
    <p><a href="#">Trang chủ</a> / Cửa hàng</p>
  </header>

  <main class="shop-container">
    <aside class="filters">
      <h2>TÌM KIẾM</h2>

      <div class="filter-group">
        <h4>Loại hoa</h4>
        <label><input type="checkbox" checked> Hoa hồng</label>
        <label><input type="checkbox"> Hoa lan</label>
        <label><input type="checkbox"> Hoa ly</label>
        <label><input type="checkbox"> Cúc</label>
        <label><input type="checkbox"> Tulip</label>
        <label><input type="checkbox"> Hướng dương</label>
      </div>

      <div class="filter-group">
        <h4>Màu sắc</h4>
        <label><input type="checkbox"> Hồng</label>
        <label><input type="checkbox"> Đỏ</label>
        <label><input type="checkbox"> Vàng</label>
        <label><input type="checkbox"> Trắng</label>
        <label><input type="checkbox" checked> Nhiều màu</label>
      </div>

      <div class="filter-group">
        <h4>Giá</h4>
        <input type="range" min="20" max="100" value="100" />
        <p>20.000đ – 100.000đ</p>
      </div>

      <div class="filter-group">
        <h4>Dịp tặng</h4>
        <label><input type="checkbox"> Đám cưới</label>
        <label><input type="checkbox"> Sinh nhật</label>
        <label><input type="checkbox" checked> Tình yêu</label>
        <label><input type="checkbox"> Tốt nghiệp</label>
        <label><input type="checkbox"> Động viên</label>
      </div>

      <div class="filter-group">
        <h4>Người nhận</h4>
        <label><input type="checkbox"> Nam</label>
        <label><input type="checkbox"> Nữ</label>
        <label><input type="checkbox"> Trẻ em</label>
        <label><input type="checkbox"> Quà tặng doanh nghiệp</label>
      </div>

      <div class="filter-group">
        <h4>Tình trạng</h4>
        <label><input type="checkbox" checked> Còn hàng</label>
        <label><input type="checkbox"> Hết hàng</label>
      </div>
    </aside>

    <section class="shop-products">
      <div class="filter-tags">
        <span>Loại hoa: Hoa hồng ❌</span>
        <span>Màu: Nhiều màu ❌</span>
        <span>Dịp: Tình yêu ❌</span>
        <span>Tình trạng: Còn hàng ❌</span>
        <a href="#">Xóa tất cả</a>
      </div>

      <div class="product-grid">
        <!-- Sản phẩm -->
        <div class="product-card">
          <span class="tag">Giảm 25%</span>
          <img src="images/flower1.png" alt="Khoảnh khắc ngọt ngào">
          <h4>Khoảnh khắc ngọt ngào</h4>
          <p>48.000đ <del>65.000đ</del></p>
          <div class="rating">⭐ 4.8</div>
          <button class="favorite">
            <i class="fa-regular fa-heart"></i>
          </button>
        </div>

        <div class="product-card">
          <span class="tag">Giảm 17%</span>
          <img src="images/flower2.png" alt="Tình yêu bất tận">
          <h4>Tình yêu bất tận</h4>
          <p>50.000đ <del>60.000đ</del></p>
          <div class="rating">⭐ 4.8</div>
          <button class="favorite">
            <i class="fa-regular fa-heart"></i>
          </button>
        </div>

        <!-- Thêm bao nhiêu tùy ý -->
      </div>

      <div class="pagination">
        <a href="#">&#60;</a>
        <a href="#" class="active">1</a>
        <a href="#">2</a>
        <a href="#">3</a>
        <a href="#">...</a>
        <a href="#">10</a>
        <a href="#">&#62;</a>
      </div>
    </section>
  </main>
</body>

<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>

<!-- Script -->
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
