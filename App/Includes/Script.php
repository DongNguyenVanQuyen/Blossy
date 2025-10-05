<!-- Includes/footer_scripts.php -->
 <script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>
<script>
  const isLoggedIn = <?= isset($_SESSION['user']) ? 'true' : 'false' ?>;
</script>

<script src="<?= BASE_URL ?>Public/Assets/Js/Script.js?v=<?= time(); ?>"></script>

