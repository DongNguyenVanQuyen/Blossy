<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Toast.css?v=<?= time()?>">
<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Admin/Order.css">

<div id="order_admin">

<?php include_once __DIR__ . '/../Layouts/Sidebar.php'; ?>

<div class="admin-main">
  <?php include_once __DIR__ . '/../Layouts/Header.php'; ?>

  <div class="order-content">
    <h2 class="order-title">📦 Quản lý đơn hàng</h2>

    <table class="order-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Khách Hàng</th>
          <th>Tổng Cộng</th>
          <th>Giảm Giá</th>
          <th>Phí Ship</th>
          <th>Phương Thức</th>
          <th>Thanh Toán</th>
          <th>Trạng Thái</th>
          <th>Ngày Tạo</th>
          <th>Thao Tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <?php
            $status = strtolower(trim($o['status'] ?? ''));
            $class = match ($status) {
            'cho_xac_nhan' => 'pending',
            'dang_giao'    => 'shipping',
            'hoan_thanh'   => 'success',
            'huy'          => 'cancel',
            default        => 'unknown'
            };
            $statusText = match ($status) {
            'cho_xac_nhan' => 'Chờ xác nhận',
            'dang_giao'    => 'Đang giao',
            'hoan_thanh'   => 'Hoàn thành',
            'huy'          => 'Đã hủy',
            default        => 'Chưa xác định'
            };

          ?>
          <tr>
            <td>#<?= htmlspecialchars($o['id']) ?></td>
            <td><?= htmlspecialchars($o['first_name'] . ' ' . $o['last_name']) ?></td>
            <td><?= number_format($o['grand_total'], 0, ',', '.') ?>đ</td>
            <td><?= number_format($o['discount_total'], 0, ',', '.') ?>đ</td>
            <td><?= number_format($o['shipping_fee'], 0, ',', '.') ?>đ</td>
            <td><?= strtoupper($o['payment_method']) ?></td>
            <td><?= ucfirst(str_replace('_', ' ', $o['payment_status'])) ?></td>
            <td>
              <span class="order-status <?= $class ?>">
                <?= match ($status) {
                    'cho_xac_nhan' => 'Chờ xác nhận',
                    'dang_giao'    => 'Đang giao',
                    'hoan_thanh'   => 'Hoàn thành',
                    'huy'          => 'Đã hủy',
                    default        => 'Chưa xác định'
                } ?>
              </span>
            </td>
            <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
            <td>
              <div class="order-update-group">
                <select class="order-status-select" data-id="<?= $o['id'] ?>">
                  <option value="cho_xac_nhan" <?= $status === 'cho_xac_nhan' ? 'selected' : '' ?>>Chờ xác nhận</option>
                  <option value="dang_giao" <?= $status === 'dang_giao' ? 'selected' : '' ?>>Đang giao</option>
                  <option value="hoan_thanh" <?= $status === 'hoan_thanh' ? 'selected' : '' ?>>Hoàn thành</option>
                  <option value="huy" <?= $status === 'huy' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
                <button class="order-btn-update" data-id="<?= (int)$o['id'] ?>">Cập nhật</button>
                <a href="index.php?controller=adminorder&action=detail&id=<?= $o['id'] ?>" class="order-btn-detail">Chi tiết</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- ✅ PHÂN TRANG -->
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="index.php?controller=adminorder&action=index&page=<?= $i ?>" 
           class="pagination-link <?= $i == $page ? 'active' : '' ?>">
           <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  </div>
</div>
</div>
<script src="<?= BASE_URL ?>Public/Assets/Js/Toast.js?v=<?= time()?>"></script>
<!-- AJAX cập nhật trạng thái -->
<script src="<?= BASE_URL ?>Public/Assets/Js/Admin/Order.js?v=<?= time() ?>"></script>
