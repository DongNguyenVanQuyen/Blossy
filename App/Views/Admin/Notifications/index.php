<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Admin/Notification.css?v=<?= time() ?>">

<div id="admin-notification">
  <?php include_once __DIR__ . '/../Layouts/Sidebar.php'; ?>

  <div class="admin-main">
    <?php include_once __DIR__ . '/../Layouts/Header.php'; ?>

    <h2 class="admin-title">📢 Quản lý Thông Báo</h2>

    <!-- 🔹 Form thêm / sửa -->
    <div class="notify-form">
      <h3 id="formTitle">Thêm thông báo mới</h3>
      <form id="createNoticeForm">
        <input type="hidden" name="id" id="notice_id">
        <div class="form-group">
          <label>Tiêu đề:</label>
          <input type="text" name="title" id="notice_title" required>
        </div>
        <div class="form-group">
          <label>Nội dung:</label>
          <textarea name="body" id="notice_body" rows="4" required></textarea>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn-primary">Lưu</button>
          <button type="button" id="cancelEdit" class="btn-cancel" style="display:none;">Hủy sửa</button>
        </div>
      </form>
    </div>

    <!-- 🔹 Danh sách -->
    <div class="notify-list">
      <h3>Danh sách thông báo</h3>
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Nội dung</th>
            <th>Loại</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($notifications as $n): ?>
            <tr data-id="<?= $n['id'] ?>">
              <td>#<?= $n['id'] ?></td>
              <td><?= htmlspecialchars($n['title']) ?></td>
              <td><?= nl2br(htmlspecialchars($n['body'])) ?></td>
              <td >
                <?php if ($n['type'] === 'order'): ?>
                  <span class="status blocked">Đơn hàng</span>
                <?php elseif ($n['type'] === 'system'): ?>
                  <span class="status active">Hệ thống</span>
                <?php else: ?>
                  <span class="status"><?= htmlspecialchars($n['type']) ?></span>
                <?php endif; ?>
              </td>


              <td><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></td>
              <td>
                <button class="btn-edit" data-id="<?= $n['id'] ?>">Sửa</button>
                <button class="btn-delete" data-id="<?= $n['id'] ?>">Xóa</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- ✅ PHÂN TRANG -->
      <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="index.php?controller=adminnotification&action=index&page=<?= $i ?>"
             class="pagination-link <?= $i == $page ? 'active' : '' ?>">
             <?= $i ?>
          </a>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</div>

<script src="<?= BASE_URL ?>Public/Assets/Js/Admin/Notification.js?v=<?= time() ?>"></script>
