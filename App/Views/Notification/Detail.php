<?php
  include_once __DIR__ . '/../../Includes/config.php';

  // Head
  include_once __DIR__ . '/../../Includes/head.php';
  include_once __DIR__ . '/../Layouts/Header.php';

?>

<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/User/Notification.css?v=<?= time() ?>">

<div class="notify-detail">
  <div class="notify-detail__container">
    <h1 class="notify-detail__title">
      <?= htmlspecialchars($message['title'] ?? 'Thông báo') ?>
    </h1>

    <div class="notify-detail__body">
      <?= nl2br(htmlspecialchars($message['body'] ?? 'Không có nội dung.')) ?>
    </div>

    <a href="<?= BASE_URL ?>index.php" class="notify-detail__back">
     Quay lại Trang Chủ
    </a>
  </div>
</div>

<?php
include_once __DIR__ . '/../Layouts/Footer.php';
include_once __DIR__ .'/../../Includes/Script.php'
?>