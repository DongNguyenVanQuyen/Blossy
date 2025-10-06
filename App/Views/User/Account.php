<?php
  include_once __DIR__ . '/../../Includes/config.php';

  // Head
  include_once __DIR__ . '/../../Includes/head.php';
  include_once __DIR__ . '/../Layouts/Header.php';
  ?>
<!-- Trang Tài Khoản - HTML Full 4 Tabs -->
<div class="my-account">
  <!-- Sidebar Tabs -->
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

  <!-- Content Panels -->
  <div class="my-account__content">
    <!-- Panel 1: Personal Information -->
    <div class="my-account__panel active" id="info">
      <h2 class="my-account__title">Tài Khoản Của Tôi</h2>
      <p class="my-account__breadcrumb">Trang chủ / Tài Khoản</p>
        <form class="my-account__form">
        <div class="my-account__form-row">
            <div>
            <label>Họ*</label>
            <input type="text" placeholder="Nhập họ" value="<?= $user['first_name'] ?? '' ?>">
            </div>
            <div>
            <label>Tên*</label>
            <input type="text" placeholder="Nhập tên" value="<?= $user['last_name'] ?? '' ?>">
            </div>
        </div>
        <div class="my-account__form-row">
            <div>
            <label>Email*</label>
            <input type="email" placeholder="Nhập email" value="<?= $user['email'] ?? '' ?>" readonly>
            </div>
            <div>
            <label>Số điện thoại*</label>
            <input type="tel" placeholder="Nhập số điện thoại" value="<?= $user['phone'] ?? '' ?>">
            </div>
        </div>
        <div class="my-account__form-row">
            <div>
            <label>Giới tính*</label>
            <select>
                <option <?= (isset($user['gender']) && $user['gender'] == 'Nữ') ? 'selected' : '' ?>>Nữ</option>
                <option <?= (isset($user['gender']) && $user['gender'] == 'Nam') ? 'selected' : '' ?>>Nam</option>
                <option <?= (isset($user['gender']) && $user['gender'] == 'Khác') ? 'selected' : '' ?>>Khác</option>
            </select>
            </div>
        </div>
        <button class="my-account__submit">Cập Nhật</button>
        </form>

    </div>

    <!-- Panel 2: My Orders -->
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

    <!-- Panel 3: Manage Address -->
    <div class="my-account__panel" id="address">
        <h2 class="my-account__title">Quản Lý Địa Chỉ</h2>
        <p class="my-account__breadcrumb">Trang chủ / Địa Chỉ</p>
        <p>Địa chỉ giao hàng:</p>
        
        <!-- Danh sách địa chỉ mẫu -->
        <div class="my-account__address-list">
            <div class="my-account__address-item">
              <p><strong>Nhà:</strong> 123 Đường Hoa, Quận 1, TP. Hồ Chí Minh</p>
            </div>
            <div class="my-account__address-item">
              <p><strong>Văn phòng:</strong> 456 Đường Hoa Hồng, Cầu Giấy, Hà Nội</p>
            </div>
        </div>

        <!-- Thêm địa chỉ mới -->
        <textarea rows="4" placeholder="Nhập địa chỉ mới..."></textarea>
        <button class="my-account__submit">Lưu Địa Chỉ</button>
    </div>

    <!-- Panel 4: Payment Method -->
    <div class="my-account__panel" id="payment">
      <h2 class="my-account__title">Phương Thức Thanh Toán</h2>
      <p class="my-account__breadcrumb">Trang chủ / Thanh Toán</p>

      <!-- Danh sách thẻ đã lưu -->
      <div class="my-account__card-list">
        <!-- Thẻ 1 -->
        <div class="my-account__card-item">
          <div class="my-account__card-info">
            <img src="<?= BASE_URL ?>Assets/Image/Icons/visa.png" alt="Visa">
            <div>
              <p>Visa **** 4242</p>
              <small>Hết hạn: 04/27</small>
            </div>
          </div>
          <button class="my-account__remove-btn">Xóa</button>
        </div>

        <!-- Thẻ 2 -->
        <div class="my-account__card-item">
          <div class="my-account__card-info">
            <img src="<?= BASE_URL ?>Assets/Image/Icons/mastercard.png" alt="Mastercard">
            <div>
              <p>Mastercard **** 1911</p>
              <small>Hết hạn: 11/25</small>
            </div>
          </div>
          <button class="my-account__remove-btn">Xóa</button>
        </div>
      </div>

      <!-- Nút thêm phương thức mới -->
       <a href="<?= BASE_URL ?>index.php?controller=auth&action=addNewCard">
        <button class="my-account__submit">Thêm Phương Thức Mới</button>
       </a>
    </div>

    <!-- Panel 5: Password Manager -->
    <div class="my-account__panel" id="password">
      <h2 class="my-account__title">Quản Lý Mật Khẩu</h2>
      <p class="my-account__breadcrumb">Trang chủ / Mật Khẩu</p>
      <form class="my-account__form">
        <label>Mật khẩu hiện tại</label>
        <input type="password" placeholder="Nhập mật khẩu hiện tại">
        <label>Mật khẩu mới</label>
        <input type="password" placeholder="Nhập mật khẩu mới">
        <label>Xác nhận mật khẩu</label>
        <input type="password" placeholder="Xác nhận mật khẩu mới">
        <button class="my-account__submit">Đổi Mật Khẩu</button>
      </form>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/../Layouts/Footer.php'; ?>
<?php include_once __DIR__ . '/../../Includes/Script.php'; ?>
<script src="<?= BASE_URL ?>Public/Assets/Js/account.js?v=<?= time(); ?>"></script>
