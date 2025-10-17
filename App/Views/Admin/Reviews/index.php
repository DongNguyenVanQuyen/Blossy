<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Admin/Review.css?v=<?= time() ?>">

<div class="admin-review">
  <?php include_once __DIR__ . '/../Layouts/Sidebar.php'; ?>
  <div class="admin-review__content">
    <?php include_once __DIR__ . '/../Layouts/Header.php'; ?>
    <h2 class="admin-title">📋 Quản lý đánh giá sản phẩm</h2>

    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Sản phẩm</th>
          <th>Người đánh giá</th>
          <th>Nội dung</th>
          <th>Sao</th>
          <th>Trạng thái</th>
          <th>Ngày</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($reviews)): ?>
          <?php foreach ($reviews as $r): ?>
            <tr data-id="<?= $r['id'] ?>">
              <td><?= $r['id'] ?></td>
              <td><?= htmlspecialchars($r['product_name']) ?></td>
              <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
              <td><?= htmlspecialchars($r['content']) ?></td>
              <td>⭐ <?= intval($r['rating']) ?></td>
              <td>
                <span class="status <?= $r['is_approved'] ? 'active' : 'hidden' ?>">
                  <?= $r['is_approved'] ? 'Hiển thị' : 'Ẩn' ?>
                </span>
              </td>
              <td><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
              <td>
                <button class="btn-toggle" data-status="<?= $r['is_approved'] ? 0 : 1 ?>">
                  <?= $r['is_approved'] ? 'Ẩn' : 'Hiện' ?>
                </button>
                <button class="btn-delete">Xóa</button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" style="text-align:center;">Không có đánh giá nào.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- PHÂN TRANG -->
    <div class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?controller=adminreview&action=index&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
</div>

<script src="<?= BASE_URL ?>Public/Assets/Js/Admin/Review.js?v=<?= time() ?>"></script>
