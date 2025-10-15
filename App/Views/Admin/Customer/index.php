<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Toast.css?v=<?= time()?>">
<link rel="stylesheet" href="<?= BASE_URL?>Public/Assets/Css/Admin/Customer.css">
<div id="customer_admin">

<?php include __DIR__ . '/../Layouts/Sidebar.php'; ?>

<div class="admin-main">
  <?php include_once __DIR__ . '/../Layouts/Header.php'; ?>

  <div class="customer-content">
    <h2 class="customer-title">👤 Quản lý khách hàng</h2>

    <table class="customer-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Họ và Tên</th>
          <th>Email</th>
          <th>SĐT</th>
          <th>Giới tính</th>
          <th>Cấp độ</th>
          <th>Tổng chi tiêu</th>
          <th>Trạng thái</th>
          <th>Ngày tạo</th>
          <th>Thao tác</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($customers as $c): ?>
          <tr>
            <td>#<?= htmlspecialchars($c['id']) ?></td>
            <td><?= htmlspecialchars(($c['last_name'] ?? '') . ' ' . ($c['first_name'] ?? '')) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars($c['phone'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($c['gender'] ?? 'Khác') ?></td>

            <!-- Cấp độ -->
            <td>
              <?php
              $level = strtolower($c['level'] ?? 'normal');
              $class = match ($level) {
                'diamond' => 'lvl-diamond',
                'gold'    => 'lvl-gold',
                'silver'  => 'lvl-silver',
                default   => 'lvl-normal'
              };
              ?>
              <span class="customer-level <?= $class ?>">
                <?= ucfirst($level) ?>
              </span>
            </td>

            <!-- Tổng chi tiêu -->
            <td><?= number_format($c['total_spent'] ?? 0, 0, ',', '.') ?>đ</td>

            <!-- Trạng thái -->
            <td>
              <?php if (!empty($c['is_blocked']) && $c['is_blocked'] == 1): ?>
                <span class="customer-status blocked">Đã khóa</span>
              <?php else: ?>
                <span class="customer-status active">Hoạt động</span>
              <?php endif; ?>
            </td>

            <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>

            <td>
              <button class="customer-btn-toggle" data-id="<?= $c['id'] ?>">
                <?= ($c['is_blocked'] == 1) ? 'Mở khóa' : 'Khóa' ?>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- PHÂN TRANG -->
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="index.php?controller=admincustomer&action=index&page=<?= $i ?>" 
           class="pagination-link <?= $i == $page ? 'active' : '' ?>">
           <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  </div>
</div>
</div>
<script src="<?= BASE_URL ?>Public/Assets/Js/Toast.js?v=<?= time()?>"></script>
<script src="<?= BASE_URL ?>Public/Assets/Js/Admin/Customer.js"></script>
