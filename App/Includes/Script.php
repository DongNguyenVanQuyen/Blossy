<!-- Includes/footer_scripts.php -->
 <body>
  <!-- Toast JS -->
<script src="<?= BASE_URL ?>Public/Assets/Js/Toast.js?v=<?= time() ?>"></script>

<?php if (!empty($_SESSION['toast'])): ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
  showToast("<?= addslashes($_SESSION['toast']['message']) ?>", "<?= $_SESSION['toast']['type'] ?>");
});
</script>
<?php unset($_SESSION['toast']); endif; ?>

 </body>
 
 <script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>
<script>
  const isLoggedIn = <?= isset($_SESSION['user']) ? 'true' : 'false' ?>;
</script>

<script src="<?= BASE_URL ?>Public/Assets/Js/Script.js?v=<?= time(); ?>"></script>

