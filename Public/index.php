<?php include_once __DIR__ . '/../App/Includes/config.php'; ?>

<!-- Head -->
<?php include_once __DIR__ . '/../App/Includes/head.php'; ?>

<!-- Header -->
<?php include_once __DIR__ . '/../App/Views/Layouts/Header.php'; ?>

<!-- Main -->
<div id="Main" class="pos-rel">
  <?php include_once __DIR__ . '/../App/Views/Home/index.php'; ?>
</div>

<!-- Footer -->
<?php include_once __DIR__ . '/../App/Views/Layouts/Footer.php'; ?>

<!-- Script -->
<?php include_once __DIR__ . '/../App/Includes/Script.php'; ?>
<script src="<?= BASE_URL ?>Public/Assets/Js/home.js"></script>
