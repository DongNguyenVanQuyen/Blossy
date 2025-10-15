<?php
require_once dirname(__DIR__, 2) . '/Models/NotificationModel.php';
$notifyModel = new NotificationModel();

$userId = null;
$notifications = [];
$notifyCount = 0;

if (isset($_SESSION['user'])) {
    $userId = (int)$_SESSION['user']['user_id'];
    $notifications = $notifyModel->getUserNotifications($userId);
    $notifyCount = count(array_filter($notifications, fn($n) => !$n['is_read']));
}
?>


<!-- header.php -->
  <div id="offer">
    <h2>Đăng Nhập để nhận được 10% giảm giá cho đơn hàng đầu tiên.</h2>
    <?php if (!isset($_SESSION['user'])): ?>
      <a href="<?= BASE_URL ?>App/Views/User/login.php">Đăng Nhập Ngay</a>
    <?php endif; ?>
  </div>

<div id="Header">
    <?php
      $isLoggedIn = isset($_SESSION['user']);
    ?>

    <div id="Logo">
      <a href="<?= BASE_URL ?>Public/index.php">    
        <ul>
          <li>BL</li>
          <img class="logoImg" src="" alt="Mùa Xuân">
          <li>S</li>
          <li>S</li>
          <li>Y</li>
        </ul> 
      </a>

    </div>

    <nav class="menu">
      <a href="<?= BASE_URL ?>index.php">Trang chủ</a>
      <a href="<?= BASE_URL ?>index.php?controller=products&action=index">Cửa hàng</a>
      <a href="<?= BASE_URL ?>index.php?controller=pages&action=about">Giới thiệu</a>
      <a href="<?= BASE_URL ?>index.php?controller=pages&action=contact">Liên hệ</a>
    </nav>


      <div id="Search-box">
        <i class="fa-solid fa-magnifying-glass" id="Search-btn"></i>
        <input 
            type="text" 
            id="Search-input" 
            name="keyword"
            placeholder="Tìm kiếm ở đây"
            value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
          />
      </div>


  <?php
  require_once __DIR__ . '/../../Models/HeaderCountModel.php';
  $model = new HeaderCountModel();

  $favCount = 0;
  $cartCount = 0;

  if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['user_id'];
    $favCount = $model->getFavoriteCount($userId);
    $cartCount = $model->getCartCount($userId);
  }
  ?>

  <div id="favourite-Cart">
    <!-- ❤️ Yêu thích -->
    <a href="<?=
        isset($_SESSION['user'])
        ? BASE_URL . 'index.php?controller=favorites&action=index'
        : BASE_URL . 'index.php?controller=auth&action=login';
    ?>">
      <i class="fa-solid fa-heart"></i>
      <?php if ($favCount > 0): ?>
        <span class="badge"><?= $favCount ?></span>
      <?php endif; ?>
    </a>

    <!-- 🛒 Giỏ hàng -->
    <a href="<?=
        isset($_SESSION['user'])
        ? BASE_URL . 'index.php?controller=cart&action=index'
        : BASE_URL . 'index.php?controller=auth&action=login';
    ?>" class="cart-link">
      <i class="fa-solid fa-cart-shopping"></i>
      <?php if ($cartCount > 0): ?>
        <span class="badge"><?= $cartCount ?></span>
      <?php endif; ?>
    </a>

    <!-- 🔔 Thông báo -->
    <div class="notify-wrapper">
      <i class="fa-solid fa-bell notify-icon"></i>
      <?php if (!empty($notifyCount) && $notifyCount > 0): ?>
        <span class="badge"><?= $notifyCount ?></span>
      <?php endif; ?>

      <!-- 🔹 Thanh thông báo -->
      <div class="notify-panel">
        <h2>Danh sách Thông Báo</h2>
        <ul class="notify-list">
          <?php if (empty($notifications)): ?>
            <li class="empty">Không có thông báo nào.</li>
          <?php else: ?>
            <?php foreach ($notifications as $n): ?>
              <li class="<?= $n['is_read'] ? 'read' : 'unread' ?>">
                <a href="index.php?controller=notification&action=open&id=<?= $n['id'] ?>">
                  <strong><?= strtoupper(htmlspecialchars($n['title'])) ?></strong>
                  <p><?= htmlspecialchars(mb_strimwidth($n['body'], 0, 80, '...')) ?></p>
                </a>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </div>
    </div>         
  </div>
  <div id="User">
      <?php if (isset($_SESSION['user'])): ?>
        <a href="<?= BASE_URL ?>index.php?controller=auth&action=Info">
        <span>Xin chào, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>

       </a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>index.php?controller=auth&action=login">Đăng Nhập</a>
        <span>/</span>
        <a href="<?= BASE_URL ?>index.php?controller=auth&action=register">Đăng Ký</a>
      <?php endif; ?>
  </div>
</div>
<div id="Time" class="pos-fx"></div>

<script src="<?= BASE_URL ?>Public/Assets/Js/list.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>Public/Assets/Js/HeaderCount.js?v=<?= time() ?>"></script>