<?php 
    include_once __DIR__ . '/../../../Includes/config.php'
?>

    <link rel="stylesheet" href="<?= BASE_URL ?>/Public/Assets/Css/reset.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/Public/Assets/Css/admin_style.css?v=<?= time() ?>">
<div class="admin-header">
  <div class="admin-header__left">
    <strong>Blossy Admin</strong>
  </div>
  <div class="admin-header__right">
    <span>Xin chào, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?></span>
  </div>
</div>
