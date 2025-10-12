<?php include __DIR__ . '/../Layouts/Header.php'; ?>

<div class="admin-content-product">
  <h1 class="page-title"><?= isset($product) ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới' ?></h1>

  <form method="POST" action="index.php?controller=adminProduct&action=save">
    <?php if (!empty($product['id'])): ?>
      <input type="hidden" name="id" value="<?= $product['id'] ?>">
    <?php endif; ?>

    <label>Tên sản phẩm</label>
    <input type="text" name="name" value="<?= $product['name'] ?? '' ?>" required>

    <label>Danh mục</label>
    <select name="category_id">
      <?php foreach ($categories as $c): ?>
        <option value="<?= $c['id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $c['id']) ? 'selected' : '' ?>>
          <?= $c['name'] ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Màu sắc</label>
    <input type="text" name="color" value="<?= $product['color'] ?? '' ?>">

    <label>Slug</label>
    <input type="text" name="slug" value="<?= $product['slug'] ?? '' ?>">

    <label>Mùa</label>
    <input type="text" name="season" value="<?= $product['season'] ?? '' ?>">

    <label>Mô tả</label>
    <textarea name="description"><?= $product['description'] ?? '' ?></textarea>

    <label>Giá bán</label>
    <input type="number" name="price" value="<?= $product['price'] ?? 0 ?>">

    <label>Giá gốc</label>
    <input type="number" name="compare_at_price" value="<?= $product['compare_at_price'] ?? 0 ?>">

    <!-- ẢNH CHÍNH -->
    <label>Ảnh đại diện (chính)</label>
    <div class="upload-row">
      <input type="file" id="main_image_file" accept="image/*">
      <button type="button" id="upload-main-btn" class="btn-upload">📤 Tải lên</button>
    </div>
    <div class="preview" id="preview-main">
      <?php if (!empty($images)): foreach ($images as $img): if ($img['is_primary'] == 1): ?>
        <img src="<?= htmlspecialchars($img['url']) ?>" alt="Ảnh chính">
      <?php endif; endforeach; endif; ?>
    </div>
    <input type="hidden" name="main_url" id="main_url">

    <!-- ẢNH PHỤ -->
    <label>Ảnh phụ (gallery)</label>
    <div class="upload-row">
      <input type="file" id="sub_image_files" multiple accept="image/*">
      <button type="button" id="upload-sub-btn" class="btn-upload">📤 Tải lên</button>
    </div>
    <div class="preview-multi" id="preview-gallery">
      <?php if (!empty($images)): foreach ($images as $img): if ($img['is_primary'] == 0): ?>
        <img src="<?= htmlspecialchars($img['url']) ?>" alt="Ảnh phụ">
      <?php endif; endforeach; endif; ?>
    </div>
    <div id="sub_urls_container"></div>

    <label>Số lượng tồn</label>
    <input type="number" name="stock" value="<?= $product['stock'] ?? 0 ?>">

    <label>Ngưỡng cảnh báo tồn thấp</label>
    <input type="number" name="threshold" value="<?= $product['low_stock_threshold'] ?? 5 ?>">

    <label>
      <input type="checkbox" name="is_active" <?= !empty($product['is_active']) ? 'checked' : '' ?>> Hiển thị sản phẩm
    </label>

    <button type="submit" class="btn btn-primary">💾 Lưu</button>
  </form>
</div>

<script src="<?= BASE_URL ?>Public/Assets/Js/Admin/FormProduct.js?v=<?= time() ?>"></script>
