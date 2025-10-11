
<?php include_once __DIR__ . '/../../Includes/config.php'; ?>

<!-- Head -->
<?php include_once __DIR__ . '/../../Includes/head.php'; ?>

<!-- Header -->
<?php include_once __DIR__ . '/../Layouts/Header.php'; ?>
<!-- Main Content -->
<div id="Main" class="pos-rel">

  <section class="hero-section pos-rel">
    <div class="hero-content">
      <h2>Gửi tặng một bó hoa tình yêu chỉ với một cú nhấp chuột</h2>
      <p>Chọn những bó hoa tươi được chúng tôi tuyển chọn kỹ lưỡng cho mọi dịp.</p>
      <button><a href="index.php?controller=products&action=index" class="btn-shop-now">Chọn Hoa</a></button>

    </div>
    
    <div class="hero-image pos-rel">
      <img class="pos-ab" src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_1/Banner_Spring_1.jpeg" alt="Bouquet 1" />
      <img class="pos-ab" src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_1/Banner_Spring_2.jpeg" alt="Bouquet 2" />
    </div>

    <!-- Banner trái -->
    <img class="hero-decor pos-ab" src="<?= BASE_URL ?>Public/Seasons/Banner_Left/Spring.png" alt="Season Decoration" />
  </section>


  <!-- ========== SECTION 2: SHOP BY FLOWERS ========== -->
  <section class="shop-flowers">
    <h2 class="section-title">Cửa Hàng <span>Hoa</span></h2>
    <!-- Danh Sach Hoa -->
      <?php
    // Tạo mảng ánh xạ tên danh mục => số lượng
    $countMap = [];
    foreach ($categoriesQuantity as $cat) {
      $countMap[$cat['category_name']] = $cat['total'];
    }
    ?>

    <div class="flower-list">

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=1" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Hoa-Bo.png" alt="Hoa bó">
        <h3>Hoa bó</h3>
        <p><?= $countMap['Hoa bó'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=2" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Hoa-Gio.png" alt="Hoa giỏ">
        <h3>Hoa giỏ</h3>
        <p><?= $countMap['Hoa giỏ'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=3" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Hoa-Cuoi.png" alt="Hoa cưới">
        <h3>Hoa cưới</h3>
        <p><?= $countMap['Hoa cưới'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=4" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Hoa-Tinh-Yeu.png" alt="Hoa Tình Yêu">
        <h3>Hoa Tình Yêu</h3>
        <p><?= $countMap['Hoa Tình Yêu'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=5" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Hoa-Sinh-Nhat.png" alt="Hoa Sinh Nhật">
        <h3>Hoa Sinh Nhật</h3>
        <p><?= $countMap['Hoa Sinh Nhật'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=6" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Cay-Van-Phong.png" alt="Cây Văn Phòng">
        <h3>Cây Văn Phòng</h3>
        <p><?= $countMap['Cây Văn Phòng'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=7" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Mau-Hoa-Moi.png" alt="Mẫu Hoa Mới">
        <h3>Mẫu Hoa Mới</h3>
        <p><?= $countMap['Mẫu Hoa Mới'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=8" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Hoa-Chuc-Mung.png" alt="Hoa Chúc Mừng">
        <h3>Hoa Chúc Mừng</h3>
        <p><?= $countMap['Hoa Chúc Mừng'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=9" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Hoa-Chia-Buon.png" alt="Hoa Chia Buồn">
        <h3>Hoa Chia Buồn</h3>
        <p><?= $countMap['Hoa Chia Buồn'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=10" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Hoa-Tot-Nghiep.png" alt="Hoa Tốt Nghiệp">
        <h3>Hoa Tốt Nghiệp</h3>
        <p><?= $countMap['Hoa Tốt Nghiệp'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=11" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Hoa-Cao-Cap.png" alt="Hoa Cao Cấp">
        <h3>Hoa Cao Cấp</h3>
        <p><?= $countMap['Hoa Cao Cấp'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=12" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Hoa-Theo-Mua.png" alt="Hoa Theo Mùa">
        <h3>Hoa Theo Mùa</h3>
        <p><?= $countMap['Hoa Theo Mùa'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=13" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Hoa-Su-Kien.png" alt="Hoa Sự Kiện">
        <h3>Hoa Sự Kiện</h3>
        <p><?= $countMap['Hoa Sự Kiện'] ?? 0 ?> sản phẩm</p>
      </a>

      <a href="<?= BASE_URL ?>index.php?controller=products&action=index&category[]=15" class="flower-item">
        <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Flowers/Khac.png" alt="Khác">
        <h3>Khác</h3>
        <p><?= $countMap['Khác'] ?? 0 ?> sản phẩm</p>
      </a>

    </div>


      <!-- Khuyến mãi -->
    <div class="promo-wrapper">
      <div class="promo-card">
        <div class="promo-content">
          <span class="discount-label">Giảm giá cố định 10%</span>
          <h3>Bộ Sưu Tập Pure Bloom</h3>
          <p>“Chào đón mọi khoảnh khắc bằng vẻ đẹp thuần khiết.”</p>
          <a href="#" class="btn-promo">Shop Now →</a>
        </div>
        <div class="promo-image">
          <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Promo/Bo-Suu-Tap.png" alt="Pure Bloom Collection">
        </div>
      </div>

      <div class="promo-card">
        <div class="promo-content">
          <span class="discount-label">Giảm giá cố định 15%</span>
          <h3>Những Bó Hoa Tươi Đẹp</h3>
          <p>“Món quà hoàn hảo cho mọi dịp.”</p>
          <a href="#" class="btn-promo">Shop Now →</a>
        </div>
        <div class="promo-image">
          <img src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_2/Promo/Bo-Hoa-Tuoi.png" alt="Lovely Fresh Bouquets">
        </div>
      </div>
    </div>
  </section>
  <!-- San Pham - Holiday -->
  <section class="featured-section">
    <div class="products-header">
      <h2>Sản Phẩm<span class="highlight"> Nổi Bật:</span></h2>
      <a href="index.php?controller=products&action=index"  class="view-all">Xem Tất Cả Sản Phẩm</a>
    </div>

    <div class="product-list">
      <?php foreach ($featuredProducts as $item): ?>
        <div class="arrival-item">
          <?php if ($item['compare_at_price'] > $item['price']): ?>
            <span class="tag-off">
              Giảm <?= round((($item['compare_at_price'] - $item['price']) / $item['compare_at_price']) * 100) ?>%
            </span>
          <?php endif; ?>
          <img src="<?= htmlspecialchars($item['url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
          <div class="info">
            <h4><?= htmlspecialchars($item['name']) ?></h4>
            <p><?= number_format($item['price'], 0, ',', '.') ?>đ 
              <?php if ($item['compare_at_price'] > $item['price']): ?>
                <del><?= number_format($item['compare_at_price'], 0, ',', '.') ?>đ</del>
              <?php endif; ?>
            </p>
            <small>⭐ <?= $item['rating'] ?> &nbsp;|&nbsp; <?= htmlspecialchars($item['description']) ?></small>
            <a href="<?= BASE_URL ?>index.php?controller=products&action=detail&id=<?= $item['id'] ?>" 
              class="shop-now btn-promo">Khám Phá Ngay</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div id="holiday">
      <img class="side-image left" src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_3/holiday_Left.png" alt="Left Flower">

      <div class="holiday-offer">
        <img class="offer-img pos-ab left-top" src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_3/offer-left-top-22.png" alt="">
          <div class="offer-content">
            <h2>Ưu Đãi Ngày Lễ</h2>
            <p>Giảm 50% - Ưu đãi giới hạn Thời Gian</p>
            <div class="countdown" id="countdown">
              <div><span id="days">00</span><br>Ngày</div>
              <div><span id="hours">00</span><br>Giờ</div>
              <div><span id="minutes">00</span><br>Phút</div>
              <div><span id="seconds">00</span><br>Giây</div>
            </div>
            <button class="shop-now">Khám Phá Ngay</button>
          </div>
        <img class="offer-img pos-ab right-bottom" src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_3/offer-right-bottom-2.png" alt="">

      </div>
      <img class="side-image right" src="<?= BASE_URL ?>Public/Assets/Image/Main/Section_3/holiday_Right-2.png" alt="Right Flower">

    </div>
  </section>
  <!-- Section 4: New Arrival + Weekly Deals-->
  <section class="section4">
    <!-- 1. New Arrival -->
     <div class="new-arrival">
       <div class="section-title"> <strong>Sản Phẩm </strong><span class="highlight">Mới:</span> </div>
        <div class="arrival-list">
          <?php foreach ($newProducts as $item): ?>
            <div class="arrival-item">
              <?php if ($item['compare_at_price'] > $item['price']): ?>
                <span class="tag-off">
                  Giảm <?= round((($item['compare_at_price'] - $item['price']) / $item['compare_at_price']) * 100) ?>%
                </span>
              <?php endif; ?>
              <img src="<?= htmlspecialchars($item['url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
              <div class="info">
                <h4><?= htmlspecialchars($item['name']) ?></h4>
                <p><?= number_format($item['price'], 0, ',', '.') ?>đ 
                  <?php if ($item['compare_at_price'] > $item['price']): ?>
                    <del><?= number_format($item['compare_at_price'], 0, ',', '.') ?>đ</del>
                  <?php endif; ?>
                </p>
                <small>⭐ <?= $item['rating'] ?> &nbsp;|&nbsp; <?= htmlspecialchars($item['description']) ?></small>
                <a href="<?= BASE_URL ?>index.php?controller=products&action=detail&id=<?= $item['id'] ?>" 
                  class="shop-now btn-promo">Khám Phá Ngay</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
     </div>              

    <!-- 2. Weekly Deals -->
    <div id="Weekly">
          <div class="weekly-deals">
            <div class="deal-text">
              <h3>Ưu Đãi Hàng Tuần</h3>
              <h2>Những đóa hoa tươi mỗi tuần để làm <span class="highlight">Bừng Sáng</span> ngày của bạn</h2>
              <p>Khám phá những bó hoa tươi mới và ưu đãi đặc biệt mỗi tuần. Hãy tạo bất ngờ cho người bạn yêu thương – hoặc tự thưởng cho chính mình!</p>
              <a href="index.php?controller=products&action=index">
                <button class="btn-shop">Khám Phá Ngay →</button>
              </a>
            </div>
        </div>
    </div>
                
  </section>
</div>




<!-- Footer -->
<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>

<!-- Script -->
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
<script src="<?= BASE_URL ?>Public/Assets/Js/home.js"></script>