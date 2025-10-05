<?php include_once __DIR__ . '/../../Includes/config.php'; ?>
<?php include_once __DIR__ . '/../../Includes/head.php'; ?>
<?php include_once __DIR__ . '/../Layouts/Header.php'; ?>
<body>
      <header class="shop-header">
        <h1>Cửa Hàng</h1>
        <p><a href="#">Trang chủ</a> / Cửa hàng</p>
      </header>

      <main class="shop-container">
            <form id="filter-form" method="GET" action="<?= BASE_URL ?>index.php">
              <input type="hidden" name="controller" value="products">
              <input type="hidden" name="action" value="index">

              <!-- ===== BỘ LỌC BÊN TRÁI ===== -->
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
                  <?php $colorsList = ['Hồng'=>'hong','Đỏ'=>'do','Vàng'=>'vang','Trắng'=>'trang','Nhiều màu'=>'nhieu'];
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

        <!-- ===== DANH SÁCH SẢN PHẨM ===== -->
        <section class="shop-products" id="product-list">

          <!-- ===== THANH ACTIVE FILTER ===== -->
          <?php
            $hasFilter = !empty($selectedCategories) || !empty($selectedColors) || !empty($priceRange);
            if ($hasFilter):
          ?>
          <div class="active-filters-bar">
            <span class="active-label">Active Filter:</span>
            <div class="tags-list">

              <!-- Loại hoa -->
              <?php foreach ($categories as $cat):
                if (in_array($cat['id'], $selectedCategories ?? [])): ?>
                  <span class="tag" data-type="category" data-value="<?= $cat['id'] ?>">
                    Flower: <?= htmlspecialchars($cat['name']) ?> <a href="#" class="remove-tag">×</a>
                  </span>
              <?php endif; endforeach; ?>

              <!-- Màu sắc -->
              <?php foreach ($colorsList as $label=>$val):
                if (in_array($val, $selectedColors ?? [])): ?>
                  <span class="tag" data-type="color" data-value="<?= $val ?>">
                    Color: <?= $label ?> <a href="#" class="remove-tag">×</a>
                  </span>
              <?php endif; endforeach; ?>

              <!-- Giá -->
              <?php if (!empty($priceRange)): ?>
                <?php
                  $priceLabels = [
                    '0-500000' => 'Price: Dưới 500k',
                    '500000-1000000' => 'Price: 500k - 1 triệu',
                    '1000000-99999999' => 'Price: Trên 1 triệu'
                  ];
                ?>
                <span class="tag" data-type="price" data-value="<?= $priceRange ?>">
                  <?= $priceLabels[$priceRange] ?? '' ?> <a href="#" class="remove-tag">×</a>
                </span>
              <?php endif; ?>

              <!-- Clear All -->
              <a href="<?= BASE_URL ?>index.php?controller=products&action=index" class="clear-all">Clear All</a>
            </div>
          </div>
          <?php endif; ?>

          <!-- ===== GRID SẢN PHẨM ===== -->
          <div class="product-grid">
            <?php foreach ($products as $product): ?>
              <?php include __DIR__ . '/_ProductCard.php'; ?>
            <?php endforeach; ?>
          </div>

          <!-- ===== PHÂN TRANG ===== -->
          <div class="pagination">
            <?php if ($currentPage > 1): ?>
              <a href="<?= BASE_URL ?>index.php?controller=products&action=index&page=<?= $currentPage - 1 ?>">&lt;</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <?php if ($i == $currentPage): ?>
                <a href="#" class="active"><?= $i ?></a>
              <?php else: ?>
                <a href="<?= BASE_URL ?>index.php?controller=products&action=index&page=<?= $i ?>"><?= $i ?></a>
              <?php endif; ?>
            <?php endfor; ?>
            <?php if ($currentPage < $totalPages): ?>
              <a href="<?= BASE_URL ?>index.php?controller=products&action=index&page=<?= $currentPage + 1 ?>">&gt;</a>
            <?php endif; ?>
          </div>
        </section>
      </main>
</body>

<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
<script src="<?= BASE_URL ?>Public/Assets/Js/List.js?v=<?= time(); ?>" type="text/javascript"></script>