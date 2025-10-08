<?php
include_once __DIR__ . '/../../Includes/config.php';
include_once __DIR__ . '/../../Includes/head.php';
include_once __DIR__ . '/../Layouts/Header.php';
?>

<!-- ========== TRANG TÀI KHOẢN NGƯỜI DÙNG ========== -->
<div class="my-account">

  <!-- ========== SIDEBAR ========== -->
  <div class="my-account-left">
    <div class="my-account__sidebar">
      <div class="my-account__tab my-account__tab--active" data-tab="info">Thông Tin Cá Nhân</div>
      <div class="my-account__tab" data-tab="orders">Đơn Hàng Của Tôi</div>
      <div class="my-account__tab" data-tab="address">Quản Lý Địa Chỉ</div>
      <div class="my-account__tab" data-tab="payment">Phương Thức Thanh Toán</div>
      <div class="my-account__tab" data-tab="password">Quản Lý Mật Khẩu</div>
      <a href="<?= BASE_URL ?>index.php?controller=auth&action=logout">
        <div class="my-account__tab">Đăng Xuất</div>
      </a>
    </div>
  </div>

  <!-- ========== NỘI DUNG CHÍNH ========== -->
  <div class="my-account__content">

    <!-- ===== 1. Thông Tin Cá Nhân ===== -->
    <div class="my-account__panel active" id="info">
      <h2 class="my-account__title">Tài Khoản Của Tôi</h2>
      <p class="my-account__breadcrumb">Trang chủ / Tài Khoản</p>

      <form class="my-account__form"
            method="POST"
            action="<?= BASE_URL ?>index.php?controller=auth&action=handleUpdateInfo">

        <div class="my-account__form-row">
          <div>
            <label>Họ*</label>
            <input type="text" name="first_name" placeholder="Nhập họ"
                   value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
          </div>
          <div>
            <label>Tên*</label>
            <input type="text" name="last_name" placeholder="Nhập tên"
                   value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
          </div>
        </div>

        <div class="my-account__form-row">
          <div>
            <label>Email*</label>
            <input type="email" name="email" placeholder="Nhập email"
                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
          </div>
          <div>
            <label>Số điện thoại*</label>
            <input type="tel" name="phone" placeholder="Nhập số điện thoại"
                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
          </div>
        </div>

        <div class="my-account__form-row">
          <div>
            <label>Giới tính*</label>
            <select name="gender">
              <option value="Nữ" <?= (isset($user['gender']) && $user['gender'] == 'Nữ') ? 'selected' : '' ?>>Nữ</option>
              <option value="Nam" <?= (isset($user['gender']) && $user['gender'] == 'Nam') ? 'selected' : '' ?>>Nam</option>
              <option value="Khác" <?= (isset($user['gender']) && $user['gender'] == 'Khác') ? 'selected' : '' ?>>Khác</option>
            </select>
          </div>
        </div>

        <button type="submit" class="my-account__submit">Cập Nhật</button>
      </form>
    </div>

    <!-- ===== 2. Đơn Hàng Của Tôi ===== -->
    <div class="my-account__panel" id="orders">
      <h2 class="my-account__title">Đơn Hàng Của Tôi</h2>
      <p class="my-account__breadcrumb">Trang chủ / Đơn Hàng</p>

      <table class="my-account__table">
        <thead>
          <tr>
            <th>Mã Đơn</th>
            <th>Ngày</th>
            <th>Trạng Thái</th>
            <th>Tổng</th>
            <th>Hành Động</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#ORD1234</td>
            <td>15/09/2025</td>
            <td class="my-account__status my-account__status--completed">Hoàn Thành</td>
            <td>2.450.000đ</td>
            <td><button class="my-account__view-btn">Xem</button></td>
          </tr>
          <tr>
            <td>#ORD1235</td>
            <td>21/09/2025</td>
            <td class="my-account__status my-account__status--pending">Đang Chờ</td>
            <td>1.780.000đ</td>
            <td><button class="my-account__view-btn">Xem</button></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ===== 3. Quản Lý Địa Chỉ ===== -->
    <div class="my-account__panel" id="address">
      <h2 class="my-account__title">Quản Lý Địa Chỉ</h2>
      <p class="my-account__breadcrumb">Trang chủ / Địa Chỉ</p>

      <?php if (!empty($addresses)): ?>
        <div class="my-account__address-list">
          <?php foreach ($addresses as $addr): ?>
            <div class="my-account__address-item">
              <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($addr['line1']) ?></p>
              <div class="my-account__address-actions">
                <a href="#"
                   class="edit"
                   onclick="editAddress(<?= $addr['id'] ?>, '<?= htmlspecialchars($addr['line1'], ENT_QUOTES) ?>')">✏️ Sửa</a>
                <a href="<?= BASE_URL ?>index.php?controller=auth&action=HandleDeleteAddress&id=<?= $addr['id'] ?>"
                   class="delete"
                   onclick="return confirm('Xóa địa chỉ này?')">🗑️ Xóa</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="my-account__no-address">Bạn chưa có địa chỉ nào.</p>
      <?php endif; ?>

      <!-- Form thêm / sửa -->
      <form method="POST"
            action="<?= BASE_URL ?>index.php?controller=auth&action=HandleSaveAddress"
            class="my-account__address-form">
        <input type="hidden" name="id" id="addressId">
        <textarea name="address" id="addressInput" rows="3" placeholder="Nhập địa chỉ..." required></textarea>
        <button type="submit" class="my-account__submit" id="saveBtn">Lưu Địa Chỉ</button>
      </form>
    </div>

    <!-- ===== 4. Phương Thức Thanh Toán ===== -->
    <div class="my-account__panel" id="payment">
      <h2 class="my-account__title">Phương Thức Thanh Toán</h2>
      <p class="my-account__breadcrumb">Trang chủ / Thanh Toán</p>

      <?php
        $userModel = new UserModel();
        $cards = $userModel->getUserCards($_SESSION['user']['user_id']);
      ?>

      <div class="my-account__card-list">
        <?php if (!empty($cards)): ?>
          <?php foreach ($cards as $card): ?>
            <div class="my-account__card-item">
              <div class="my-account__card-info">
                <img src="<?= BASE_URL ?>Assets/Image/Icons/<?= strtolower($card['card_brand']) ?>.png" alt="<?= $card['card_brand'] ?>">
                <div>
                  <p><?= htmlspecialchars($card['card_brand']) ?> **** <?= htmlspecialchars($card['card_number_last4']) ?></p>
                  <small>Hết hạn: <?= htmlspecialchars($card['expiry_date']) ?></small>
                </div>
              </div>
              <a href="<?= BASE_URL ?>index.php?controller=auth&action=deleteCard&id=<?= $card['id'] ?>"
                 class="my-account__remove-btn"
                 onclick="return confirm('Xóa thẻ này?')">Xóa</a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>Chưa có thẻ nào được lưu.</p>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>index.php?controller=auth&action=addNewCard">
          <button class="my-account__submit">Thêm Phương Thức Mới</button>
        </a>
      </div>
    </div>

    <!-- ===== 5. Quản Lý Mật Khẩu ===== -->
    <div class="my-account__panel" id="password">
      <h2 class="my-account__title">Quản Lý Mật Khẩu</h2>
      <p class="my-account__breadcrumb">Trang chủ / Mật Khẩu</p>

      <form class="my-account__form"
            method="POST"
            action="<?= BASE_URL ?>index.php?controller=auth&action=HandleChangePassword">
        <label>Mật khẩu hiện tại</label>
        <input type="password" name="current_password" placeholder="Nhập mật khẩu hiện tại" required>

        <label>Mật khẩu mới</label>
        <input type="password" name="new_password" placeholder="Nhập mật khẩu mới" required>

        <label>Xác nhận mật khẩu</label>
        <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu mới" required>

        <button type="submit" class="my-account__submit">Đổi Mật Khẩu</button>
      </form>
    </div>

  </div> <!-- /my-account__content -->
</div> <!-- /my-account -->

<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
<script src="<?= BASE_URL ?>Public/Assets/Js/account.js?v=<?= time(); ?>"></script>
