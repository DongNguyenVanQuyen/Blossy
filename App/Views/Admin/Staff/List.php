<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Admin/Staff.css?v=<?= time() ?>">

<div id="admin-staff">
  <?php include_once __DIR__ . '/../Layouts/Sidebar.php'; ?>

  <div class="admin-staff">
    <?php include_once __DIR__ . '/../Layouts/Header.php'; ?>

    <h2 class="admin-title">👥 Quản lý Nhân Viên</h2>

    <!-- 🔹 DANH SÁCH -->
    <div class="staff-list">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Điện thoại</th>
            <th>Giới tính</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($staffs)): ?>
            <?php foreach ($staffs as $s): ?>
              <tr>
                <td>#<?= $s['id'] ?></td>
                <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars($s['phone']) ?></td>
                <td><?= htmlspecialchars($s['gender'] ?? '-') ?></td>
                <td>
                  <button 
                    class="staff-toggle toggle <?= $s['is_blocked'] ? 'blocked' : 'active' ?>" 
                    data-id="<?= $s['id'] ?>">
                    <?= $s['is_blocked'] ? 'Đã khóa' : 'Hoạt động' ?>
                  </button>

                </td>
                <td>
                  <button class="staff-edit btn-edit"
                    data-id="<?= $s['id'] ?>"
                    data-fname="<?= htmlspecialchars($s['first_name']) ?>"
                    data-lname="<?= htmlspecialchars($s['last_name']) ?>"
                    data-email="<?= htmlspecialchars($s['email']) ?>"
                    data-phone="<?= htmlspecialchars($s['phone']) ?>"
                    data-gender="<?= $s['gender'] ?>"
                  >Sửa</button>
                  <a href="index.php?controller=adminstaff&action=delete&id=<?= $s['id'] ?>" class="btn-delete">Xóa</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" style="text-align:center;">Chưa có nhân viên nào</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- 🔹 FORM THÊM / SỬA -->
    <div class="staff-form">
      <h3 id="staffFormTitle">Thêm nhân viên mới</h3>
      <form method="POST" id="staffForm" action="index.php?controller=adminstaff&action=create">
        <input type="hidden" name="id" id="staff_id">

        <div class="form-row">
          <div class="form-group">
            <label>Họ</label>
            <input type="text" name="first_name" id="staff_fname" required>
          </div>
          <div class="form-group">
            <label>Tên</label>
            <input type="text" name="last_name" id="staff_lname" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="staff_email" required>
          </div>
          <div class="form-group">
            <label>Điện thoại</label>
            <input type="text" name="phone" id="staff_phone">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Giới tính</label>
            <select name="gender" id="staff_gender">
              <option value="Nam">Nam</option>
              <option value="Nữ">Nữ</option>
            </select>
          </div>
          <div class="form-group">
            <label>Mật khẩu (nếu thêm mới hoặc đổi)</label>
            <input type="password" name="password" id="staff_password">
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" id="staffSubmit" class="btn btn-primary">+ Thêm Nhân Viên</button>
          <button type="button" id="staffCancel" class="btn btn-cancel" style="display:none;">❌ Hủy</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll(".staff-edit").forEach(btn => {
  btn.addEventListener("click", () => {
    document.getElementById("staffFormTitle").textContent = "✏️ Cập nhật Nhân Viên";
    document.getElementById("staffSubmit").textContent = "💾 Lưu Thay Đổi";
    document.getElementById("staffCancel").style.display = "inline-block";
    document.getElementById("staffForm").action = "index.php?controller=adminstaff&action=edit";

    document.getElementById("staff_id").value = btn.dataset.id;
    document.getElementById("staff_fname").value = btn.dataset.fname;
    document.getElementById("staff_lname").value = btn.dataset.lname;
    document.getElementById("staff_email").value = btn.dataset.email;
    document.getElementById("staff_phone").value = btn.dataset.phone;
    document.getElementById("staff_gender").value = btn.dataset.gender;
  });
});

document.getElementById("staffCancel").addEventListener("click", () => {
  const form = document.getElementById("staffForm");
  form.reset();
  form.action = "index.php?controller=adminstaff&action=create";
  document.getElementById("staffFormTitle").textContent = "Thêm nhân viên mới";
  document.getElementById("staffSubmit").textContent = "+ Thêm Nhân Viên";
  document.getElementById("staffCancel").style.display = "none";
});

document.querySelectorAll(".staff-toggle").forEach(btn => {
  btn.addEventListener("click", async () => {
    const id = btn.dataset.id;
    const res = await fetch("index.php?controller=adminstaff&action=toggle", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: "id=" + id
    });
    const data = await res.json();
    if (data.success) location.reload();
  });
});
</script>
