<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Admin/Voucher.css?v=<?= time() ?>">
<div id="voucher_admin">
<?php include_once __DIR__ . '/../Layouts/Sidebar.php'; ?>
<div class="admin-main">
<?php include_once __DIR__ . '/../Layouts/Header.php'; ?>

<div class="voucher-content">
  <h2 class="voucher-title">Quản lý Voucher</h2>
  <button id="addVoucherBtn" class="voucher-add-btn">Thêm Voucher</button>

  <table class="voucher-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Mã</th>
        <th>Loại</th>
        <th>Giá trị</th>
        <th>Giảm tối đa</th>
        <th>Đơn tối thiểu</th>
        <th>Số lượng</th>
        <th>Giới hạn/người</th>
        <th>Thời gian</th>
        <th>Kích hoạt</th>
        <th>Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($vouchers as $v): ?>
      <tr>
        <td>#<?= $v['id'] ?></td>
        <td><?= htmlspecialchars($v['code']) ?></td>
        <td><?= htmlspecialchars($v['type']) ?></td>
        <td><?= number_format($v['value'], 0, ',', '.') ?></td>
        <td><?= $v['max_discount'] ? number_format($v['max_discount'], 0, ',', '.') : '-' ?></td>
        <td><?= number_format($v['min_order_total'], 0, ',', '.') ?></td>
        <td><?= $v['total_quantity'] ?></td>
        <td><?= $v['per_user_limit'] ?></td>
        <td><?= date('d/m/Y', strtotime($v['starts_at'])) ?> - <?= date('d/m/Y', strtotime($v['ends_at'])) ?></td>
        <td>
          <button 
            class="voucher-toggle <?= $v['is_active'] ? 'status-active' : 'status-inactive' ?>" 
            data-id="<?= $v['id'] ?>">
            <?= $v['is_active'] ? 'Hoạt động' : 'Đang Tắt' ?>
          </button>
        </td>

        <td>
          <button class="voucher-edit btn-edit" data-id="<?= $v['id'] ?>"
            data-code="<?= $v['code'] ?>"
            data-type="<?= $v['type'] ?>"
            data-value="<?= $v['value'] ?>"
            data-max="<?= $v['max_discount'] ?>"
            data-min="<?= $v['min_order_total'] ?>"
            data-total="<?= $v['total_quantity'] ?>"
            data-limit="<?= $v['per_user_limit'] ?>"
            data-start="<?= $v['starts_at'] ?>"
            data-end="<?= $v['ends_at'] ?>"
            data-active="<?= $v['is_active'] ?>">Sửa</button>
          <a href="index.php?controller=adminvoucher&action=delete&id=<?= $v['id'] ?>" class="voucher-delete btn-delete">Xóa</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="voucher-pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="index.php?controller=adminvoucher&action=index&page=<?= $i ?>" 
         class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
</div>
</div>
</div>

<!-- 🔹 FORM POPUP -->
<div class="voucher-modal" id="voucherModal">
  <div class="voucher-modal-content">
    <h3 id="voucherModalTitle">Thêm Voucher</h3>
    <form id="voucherForm" method="POST" action="index.php?controller=adminvoucher&action=create">
      <input type="hidden" name="id" id="voucherId">
      <div class="form-group">
        <label>Mã code</label>
        <input type="text" name="code" id="voucherCode" required>
      </div>

      <div class="form-group">
        <label>Loại</label>
        <select name="type" id="voucherType" required>
          <option value="percent">Phần trăm (%)</option>
          <option value="amount">Số tiền (VNĐ)</option>
        </select>
      </div>

      <div class="form-group">
        <label>Giá trị</label>
        <input type="number" name="value" id="voucherValue" required>
      </div>

      <div class="form-group">
        <label>Giảm tối đa</label>
        <input type="number" name="max_discount" id="voucherMax">
      </div>

      <div class="form-group">
        <label>Đơn tối thiểu</label>
        <input type="number" name="min_order_total" id="voucherMin">
      </div>

      <div class="form-group">
        <label>Tổng số lượng</label>
        <input type="number" name="total_quantity" id="voucherTotal">
      </div>

      <div class="form-group">
        <label>Giới hạn mỗi người</label>
        <input type="number" name="per_user_limit" id="voucherLimit">
      </div>

      <div class="form-group">
        <label>Ngày bắt đầu</label>
        <input type="date" name="starts_at" id="voucherStart" required>
      </div>

      <div class="form-group">
        <label>Ngày kết thúc</label>
        <input type="date" name="ends_at" id="voucherEnd" required>
      </div>

      <div class="form-group">
        <label><input type="checkbox" name="is_active" id="voucherActive"> Kích hoạt</label>
      </div>

      <div class="voucher-form-actions">
        <button type="submit" id="voucherSubmit">Lưu</button>
        <button type="button" id="closeModal">Hủy</button>
      </div>
    </form>
  </div>
</div>
 <script src="<?= BASE_URL ?>Public/Assets/Js/Toast.js?v=<?= time() ?>"></script>
  <?php if (!empty($_SESSION['toast'])): ?>
    <script>
      showToast("<?= addslashes($_SESSION['toast']['message']) ?>","<?= $_SESSION['toast']['type'] ?>");
    </script>
    <?php unset($_SESSION['toast']); ?>
  <?php endif; ?>
<script src="<?= BASE_URL ?>Public/Assets/Js/Admin/Voucher.js?v=<?= time() ?>"></script>