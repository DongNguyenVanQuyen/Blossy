<?php
include_once __DIR__ . '/../../Includes/config.php';
?>

<body>
<div class="admin-container">
  <?php include __DIR__ . '/Layouts/Sidebar.php'; ?>

  <div class="admin-main">
    <?php include __DIR__ . '/Layouts/Header.php'; ?>

    <div class="admin-content">
      <h1 class="page-title">Bảng điều khiển</h1>

      <!-- Cards -->
      <div class="card-grid">
        <div class="card">
          <p class="card-label">Sản phẩm</p>
          <h3 class="card-value"><?= (int)$stats['products'] ?></h3>
        </div>
        <div class="card">
          <p class="card-label">Đơn hàng</p>
          <h3 class="card-value"><?= (int)$stats['orders'] ?></h3>
        </div>
        <div class="card">
          <p class="card-label">Khách hàng</p>
          <h3 class="card-value"><?= (int)$stats['customers'] ?></h3>
        </div>
        <div class="card">
          <p class="card-label">Doanh thu</p>
          <h3 class="card-value"><?= number_format((int)$stats['revenue'], 0, ',', '.') ?>đ</h3>
        </div>
      </div>

      <div class="grid-2">
        <!-- Recent Orders -->
        <div class="panel">
          <div class="panel-head">
            <h3>Đơn hàng gần đây</h3>
            <a class="link" href="?controller=orders&action=index">Xem tất cả</a>
          </div>
          <table class="table">
            <thead>
              <tr>
                <th>Mã</th><th>Ngày</th><th>Trạng thái</th><th>Phương thức</th><th>Tổng</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentOrders as $o): ?>
                <tr>
                     <td>#<?= htmlspecialchars($o['code']) ?></td>
                    <td><?= htmlspecialchars($o['created_at_fmt']) ?></td>
                    <td>
                    <?php
                        $statusMap = [
                        'cho_xac_nhan' => 'Chờ xác nhận',
                        'dang_giao'    => 'Đang giao hàng',
                        'hoan_thanh'   => 'Hoàn thành',
                        'da_huy'       => 'Đã hủy'
                        ];
                        echo $statusMap[$o['status']] ?? ucfirst($o['status']);
                    ?>
                    </td>
                    <td>
                    <?php
                        $methodMap = [
                        'cod'    => 'Tiền mặt',
                        'paypal' => 'PayPal',
                        'visa'   => 'Thẻ Visa',
                        'apple'  => 'Apple Pay'
                        ];
                        echo $methodMap[strtolower($o['payment_method'])] ?? $o['payment_method'];
                    ?>
                    </td>

                    <td><?= number_format((int)$o['grand_total'], 0, ',', '.') ?>đ</td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($recentOrders)): ?>
                <tr><td colspan="5" style="text-align:center;">Chưa có đơn hàng</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Top products -->
        <div class="panel">
          <div class="panel-head">
            <h3>Sản phẩm bán chạy</h3>
            <a class="link" href="?controller=products&action=index">Quản lý</a>
          </div>
          <table class="table">
            <thead>
              <tr><th>Tên</th><th>SL bán</th><th>Doanh thu</th></tr>
            </thead>
            <tbody>
              <?php foreach ($topProducts as $p): ?>
                <tr>
                  <td><?= htmlspecialchars($p['name']) ?></td>
                  <td><?= (int)$p['qty_sold'] ?></td>
                  <td><?= number_format((int)$p['revenue'], 0, ',', '.') ?>đ</td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($topProducts)): ?>
                <tr><td colspan="3" style="text-align:center;">Chưa có dữ liệu</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Revenue mini chart (canvas thuần) -->
      <div class="panel">
        <div class="panel-head"><h3>Doanh thu theo tháng</h3></div>
        <canvas id="revChart" height="130"></canvas>
      </div>
    </div>
  </div>
</div>
<canvas id="revChart"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById("revChart"), {
  type: "bar",
  data: {
    labels: <?= json_encode(array_column($revMonth, 'mth')) ?>,
    datasets: [{
      label: "Doanh thu (VNĐ)",
      data: <?= json_encode(array_column($revMonth, 'revenue')) ?>,
      backgroundColor: "#b4662a"
    }]
  },
  options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>
</body>

