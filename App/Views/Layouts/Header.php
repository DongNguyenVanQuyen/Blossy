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


<div id="favourite-Cart">
  <!-- Yêu thích -->
  <a 
    href="<?php 
      if (isset($_SESSION['user'])) {
        echo BASE_URL . 'index.php?controller=favorites&action=index';
      } else {
        echo BASE_URL . 'index.php?controller=auth&action=login';
      }
    ?>"
  >
    <i class="fa-solid fa-heart"></i>
  </a>

  <!-- Giỏ hàng -->
  <a 
    href="<?php 
      if (isset($_SESSION['user'])) {
        echo BASE_URL . 'index.php?controller=cart&action=index';
      } else {
        echo BASE_URL . 'index.php?controller=auth&action=login';
      }
    ?>"
    class="cart-link"
  >
    <i class="fa-solid fa-cart-shopping"></i>
  </a>
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
</div>
<div id="Time" class="pos-fx"></div>

<script src="<?= BASE_URL ?>Public/Assets/Js/list.js?v=<?= time() ?>"></script>