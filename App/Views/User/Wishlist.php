<?php
  include_once __DIR__ . '/../../Includes/config.php';

  // Head
  include_once __DIR__ . '/../../Includes/head.php';
  include_once '../Layouts/Header.php';
  ?>

<!-- Trang Yêu Thích -->
<div class="wishlist">
  <div class="wishlist__header">
    <h1 class="wishlist__title">Danh Sách Yêu Thích</h1>
    <p class="wishlist__breadcrumb">Trang chủ / Yêu Thích</p>
  </div>

  <div class="wishlist__table">
    <div class="wishlist__table-head">
      <div>Sản Phẩm</div>
      <div>Giá</div>
      <div>Ngày Thêm</div>
      <div>Trạng Thái Kho</div>
      <div></div>
    </div>

    <!-- Wishlist Item -->
    <div class="wishlist__row">
      <div class="wishlist__product">
        <img src="path/to/image1.jpg" alt="Lovely Day">
        <div>
          <small>Giỏ Hoa</small>
          <p>Lovely Day</p>
        </div>
      </div>
      <div>1.350.000đ</div>
      <div>12 Tháng 01, 2025</div>
      <div class="wishlist__stock wishlist__stock--in">Còn Hàng</div>
      <div class="wishlist__actions">
        <button class="wishlist__add-btn">Thêm Vào Giỏ</button>
        <span class="wishlist__remove">&times;</span>
      </div>

      <!-- Bản sao ví dụ -->
      <div class="wishlist__product">
        <img src="path/to/image1.jpg" alt="Lovely Day">
        <div>
          <small>Giỏ Hoa</small>
          <p>Lovely Day</p>
        </div>
      </div>
      <div>1.350.000đ</div>
      <div>12 Tháng 01, 2025</div>
      <div class="wishlist__stock wishlist__stock--in">Còn Hàng</div>
      <div class="wishlist__actions">
        <button class="wishlist__add-btn">Thêm Vào Giỏ</button>
        <span class="wishlist__remove">&times;</span>
      </div>
    </div>

    <!-- Copy cho từng sản phẩm thực tế -->
    <!-- ... -->
  </div>

  <div class="wishlist__footer">
    <div class="wishlist__link">
      <label>Liên Kết Yêu Thích:</label>
      <input type="text" value="https://www.flowrry.com" readonly>
    </div>
    <div class="wishlist__buttons">
      <button class="wishlist__copy">Sao Chép Liên Kết</button>
      <button class="wishlist__clear">Xóa Danh Sách</button>
      <button class="wishlist__addall">Thêm Tất Cả Vào Giỏ</button>
    </div>
  </div>
</div>

<?php include_once '../Layouts/Footer.php'; ?>
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
