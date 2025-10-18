

<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Admin/Promotion.css?v=<?= time() ?>">

<div id="admin-promotion">
  <?php include_once __DIR__ . '/../Layouts/Sidebar.php'; ?>

  <div class="admin-promotion">
    <?php include_once __DIR__ . '/../Layouts/Header.php'; ?>

    <h2 class="admin-title">🌸 Quản lý Khuyến Mãi</h2>

    <!-- 🔹 DANH SÁCH KHUYẾN MÃI -->
    <div class="admin-promotion__list">
      <h3>Danh sách khuyến mãi hiện có</h3>
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Mã</th>
            <th>Giảm (%)</th>
            <th>Bắt đầu</th>
            <th>Kết thúc</th>
            <th>Kích hoạt</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($promotions)): ?>
            <?php foreach ($promotions as $promo): ?>
              <tr>
                <td>#<?= $promo['id'] ?></td>
                <td><?= htmlspecialchars($promo['name']) ?></td>
                <td><?= htmlspecialchars($promo['code'] ?? '-') ?></td>
                <td><?= htmlspecialchars($promo['discount_percent']) ?>%</td>
                <td><?= htmlspecialchars($promo['starts_at']) ?></td>
                <td><?= htmlspecialchars($promo['ends_at']) ?></td>
                <td>
                  <button 
                    class="toggle <?= $promo['is_active'] ? 'active' : 'blocked' ?>" 
                    data-id="<?= $promo['id'] ?>">
                    <?= $promo['is_active'] ? 'Hoạt động' : 'Đang Tắt' ?>
                  </button>
                </td>
                <td>
                  <button class="promotion-edit btn-edit"
                    data-id="<?= $promo['id'] ?>"
                    data-name="<?= htmlspecialchars($promo['name']) ?>"
                    data-code="<?= htmlspecialchars($promo['code']) ?>"
                    data-discount="<?= $promo['discount_percent'] ?>"
                    data-start="<?= $promo['starts_at'] ?>"
                    data-end="<?= $promo['ends_at'] ?>"
                    data-active="<?= $promo['is_active'] ?>">Sửa</button>

                  <a href="index.php?controller=adminpromotion&action=delete&id=<?= $promo['id'] ?>"
                     onclick="return confirm('Bạn có chắc muốn xóa khuyến mãi này?')" 
                     class="btn-delete">Xóa</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8" style="text-align:center;">Chưa có khuyến mãi nào</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- 🔹 FORM THÊM / SỬA -->
    <div class="admin-promotion__form">
      <h3 id="promotionFormTitle">Thêm khuyến mãi mới</h3>

      <form method="POST" id="promotionForm" action="index.php?controller=adminpromotion&action=create">
        <input type="hidden" name="id" id="promotion_id">

        <div class="form-row">
          <div class="form-group">
            <label for="name">Tên chương trình*</label>
            <input type="text" name="name" id="promotion_name" required placeholder="VD: Giảm giá mùa hè">
          </div>

          <div class="form-group">
            <label for="code">Mã (tùy chọn)</label>
            <input type="text" name="code" id="promotion_code" placeholder="VD: SUMMER2025">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="discount_percent">Giảm giá (%)</label>
            <input type="number" name="discount_percent" id="promotion_discount" min="1" max="100" required>
          </div>

          <div class="form-group">
            <label for="starts_at">Ngày bắt đầu*</label>
            <input type="datetime-local" name="starts_at" id="promotion_start" required>
          </div>

          <div class="form-group">
            <label for="ends_at">Ngày kết thúc*</label>
            <input type="datetime-local" name="ends_at" id="promotion_end" required>
          </div>
        </div>

        <div class="form-group checkbox">
          <label><input type="checkbox" name="is_active" id="promotion_active"> Kích hoạt</label>
        </div>

        <div class="form-actions">
          <button type="submit" id="promotionSubmitBtn" class="btn btn-primary">Thêm Khuyến Mãi</button>
          <button type="button" id="promotionCancelBtn" class="btn btn-cancel" style="display:none;">Hủy</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="<?= BASE_URL ?>Public/Assets/Js/Admin/Promotion.js?v=<?= time() ?>"></script>