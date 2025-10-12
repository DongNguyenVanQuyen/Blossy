<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Quản lý sản phẩm | Blossy Admin') ?></title>
</head>

<body>
  <div class="admin-container">

    <!-- SIDEBAR (layout dùng chung) -->
    <?php include __DIR__ . '/../Layouts/Sidebar.php'; ?>

    <main class="admin-main">
      <!-- HEADER (layout dùng chung) -->
      <?php include __DIR__ . '/../Layouts/Header.php'; ?>

      <!-- NỘI DUNG CHÍNH -->
      <section class="admin-content">
        <header class="admin-header">
          <h1 class="page-title">Quản lý sản phẩm</h1>
          <a href="?controller=adminProduct&action=edit" class="btn btn-primary">+ Thêm sản phẩm</a>
        </header>

        <article class="product-table">
          <table class="table">
            <thead>
              <tr>
                <th>Ảnh</th>
                <th>Tên</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                  <tr>
                    <td><img src="<?= htmlspecialchars($p['image_url']) ?>" alt="Ảnh sản phẩm" width="60" height="60"></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['category_name']) ?></td>
                    <td><?= number_format($p['price'], 0, ',', '.') ?>đ</td>
                    <td><?= (int)$p['stock'] ?></td>
                    <td>
                      <?= $p['is_active']
                        ? '<span class="status-active">Hiển thị</span>'
                        : '<span class="status-inactive">Ẩn</span>' ?>
                    </td>
                    <td>
                      <a href="?controller=adminProduct&action=edit&id=<?= $p['id'] ?>" class="btn-sm btn-edit">Sửa</a>
                      <a href="?controller=adminProduct&action=delete&id=<?= $p['id'] ?>"
                         class="btn-sm btn-delete"
                         onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Xóa</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" style="text-align:center;">Chưa có sản phẩm nào.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </article>

        <!-- PHÂN TRANG -->
        <?php if ($totalPages > 1): ?>
          <nav class="pagination" aria-label="Phân trang sản phẩm">
            <!-- Nút Trang Trước -->
            <?php if ($page > 1): ?>
              <a href="?controller=adminProduct&action=index&page=<?= $page - 1 ?>" 
                 class="pagination-link prev" 
                 aria-label="Trang trước">&laquo;</a>
            <?php endif; ?>

            <!-- Các trang -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <a href="?controller=adminProduct&action=index&page=<?= $i ?>"
                 class="pagination-link <?= $i == $page ? 'active' : '' ?>"
                 aria-label="Trang <?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>

            <!-- Nút Trang Sau -->
            <?php if ($page < $totalPages): ?>
              <a href="?controller=adminProduct&action=index&page=<?= $page + 1 ?>" 
                 class="pagination-link next" 
                 aria-label="Trang sau">&raquo;</a>
            <?php endif; ?>
          </nav>
        <?php endif; ?>
      </section>

      <!-- FOOTER -->
      <footer class="admin-footer">
        <p>© <?= date('Y') ?> Blossy Admin Panel — All Rights Reserved.</p>
      </footer>
    </main>
  </div>

  <!-- JS Toast -->
  <script src="<?= BASE_URL ?>Public/Assets/Js/Toast.js?v=<?= time() ?>"></script>
  <?php if (!empty($_SESSION['toast'])): ?>
    <script>
      showToast("<?= addslashes($_SESSION['toast']['message']) ?>","<?= $_SESSION['toast']['type'] ?>");
    </script>
    <?php unset($_SESSION['toast']); ?>
  <?php endif; ?>
</body>
</html>
