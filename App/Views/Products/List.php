<?php include_once __DIR__ . '/../../Includes/config.php'; ?>
<?php include_once __DIR__ . '/../../Includes/head.php'; ?>
<?php include_once __DIR__ . '/../Layouts/Header.php'; ?>
<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Products/Products.css?v=<?= time() ?>">
<body>

  <header class="shop-header">
    <h1>Cửa Hàng</h1>
    <p><a href="<?= BASE_URL ?>">Trang chủ</a> / Cửa hàng</p>
  </header>

  <main class="shop-container">

    <!-- ====== FILTER SIDEBAR ====== -->
    <form id="filter-form">
      <!-- Sử dụng AJAX để xử lý lọc và phân trang -->
      
      <aside class="filters">
        <h2>TÌM KIẾM</h2>

        <!-- Loại hoa -->
        <div class="filter-group">
          <h4>Loại hoa</h4>
          <label>
            <input type="checkbox" name="category[]" value="all"
              <?= (empty($selectedCategories) || in_array("all", $selectedCategories)) ? 'checked' : '' ?>>
            Tất cả
          </label>
          <?php foreach ($categories as $cat): ?>
            <label>
              <input type="checkbox" name="category[]" value="<?= $cat['id'] ?>"
                <?= in_array($cat['id'], $selectedCategories ?? []) ? 'checked' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </label>
          <?php endforeach; ?>
        </div>

        <!-- Màu sắc -->
        <div class="filter-group">
          <h4>Màu sắc</h4>
          <?php 
            $colorsList = [
              'Hồng'  => 'Hồng',
              'Đỏ'    => 'Đỏ',
              'Vàng'  => 'Vàng',
              'Trắng' => 'Trắng',
              'Xanh'  => 'Xanh lá', 
              'Cam'   => 'Cam',
              'Tím'   => 'Tím',
              'Tất cả'=> 'all'
            ];
            foreach ($colorsList as $label=>$val): ?>
            <label>
              <input type="checkbox" name="color[]" value="<?= $val ?>"
                <?= in_array($val, $selectedColors ?? []) ? 'checked' : '' ?>>
              <?= $label ?>
            </label>
          <?php endforeach; ?>
        </div>

        <!-- Giá -->
        <div class="filter-group">
          <h4>Giá</h4>
          <select name="price_range">
            <option value="">Tất cả</option>
            <option value="0-500000" <?= $priceRange=='0-500000'?'selected':'' ?>>Dưới 500.000đ</option>
            <option value="500000-1000000" <?= $priceRange=='500000-1000000'?'selected':'' ?>>500k - 1 triệu</option>
            <option value="1000000-99999999" <?= $priceRange=='1000000-99999999'?'selected':'' ?>>Trên 1 triệu</option>
          </select>
        </div>

        <div class="filter-actions">
          <button type="submit" class="filter-btn">Lọc sản phẩm</button>
        </div>
      </aside>
    </form>

    <!-- ====== BÊN PHẢI: DANH SÁCH SẢN PHẨM ====== -->
    <div class="shop-right">
      
      <!-- Thanh Active Filter full width -->
      <div class="active-filters-bar">
        <span class="active-label">Active Filter:</span>
        <div class="tags-list">
          <?php if (!empty($selectedCategories) && !in_array('all', $selectedCategories)): ?>
            <?php foreach ($selectedCategories as $catId): ?>
              <?php 
                $catName = '';
                foreach ($categories as $cat) {
                  if ($cat['id'] == $catId) { $catName = $cat['name']; break; }
                }
              ?>
              <span class="tag"><?= htmlspecialchars($catName) ?> <a href="#">×</a></span>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if (!empty($selectedColors)): ?>
            <?php foreach ($selectedColors as $color): ?>
              <span class="tag"><?= ucfirst($color) ?> <a href="#">×</a></span>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if (!empty($priceRange)): ?>
            <span class="tag">
              <?php 
                if ($priceRange == '0-500000') echo 'Dưới 500k';
                elseif ($priceRange == '500000-1000000') echo '500k - 1 triệu';
                else echo 'Trên 1 triệu';
              ?>
              <a href="#">×</a>
            </span>
          <?php endif; ?>

          <a href="<?= BASE_URL ?>index.php?controller=products&action=index" class="clear-all">Clear All</a>
        </div>
      </div>

      <!-- Danh sách sản phẩm -->
      <section class="shop-products" id="product-list">
        <div class="product-grid">
        <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
          <?php include __DIR__ . '/_ProductCard.php'; ?>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-products">Không tìm thấy sản phẩm nào</div>
      <?php endif; ?>

        </div>
           <!-- Phân trang -->
          <?php include_once __DIR__ . '/_Pagination.php'; ?>


       
      </section>
    </div>
  </main>
      <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>

<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
