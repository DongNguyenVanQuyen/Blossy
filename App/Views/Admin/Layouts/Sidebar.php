<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Toast.css?v=<?= time()?>">
<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Admin/Dashboard.css?v=<?= time()?>">
<aside class="admin-sidebar">
  <h2 class="admin-logo">BLOSSY</h2>
  <nav>
    <a href="?controller=admin&action=dashboard" class="active">Bảng Điều Khiển</a>
    <a href="?controller=adminproduct&action=index">🌸 Sản phẩm</a>
    <a href="?controller=adminorder&action=index">📦 Đơn hàng</a>
    <a href="?controller=admincustomer&action=index">👥 Khách hàng</a>
    <a href="?controller=adminvoucher&action=index">🎟️ Voucher</a>
    <a href="?controller=adminpromotion&action=index">💐 Khuyến mãi</a>
    <a href="?controller=adminstaff&action=index">👨‍💼 Nhân viên</a>
    <a href="?controller=reviews&action=index">⭐ Đánh giá</a>
    <a href="?controller=adminnotification&action=index">Thông báo</a>
    <a href="<?= BASE_URL ?>index.php?controller=auth&action=logout">🚪 Đăng xuất</a>
  </nav>
</aside>
<script src="<?= BASE_URL ?>/Public/Assets/Js/Toast.js?v=<?= time()?>"></script>
  <?php if (!empty($_SESSION['toast'])): ?>
    <script>
      showToast("<?= addslashes($_SESSION['toast']['message']) ?>","<?= $_SESSION['toast']['type'] ?>");
    </script>
    <?php unset($_SESSION['toast']); ?>
  <?php endif; ?>