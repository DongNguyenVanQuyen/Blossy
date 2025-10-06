<?php
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
?>
<?php if ($totalPages > 1): ?>
  <div class="pagination" id="pagination">
    <?php if ($page > 1): ?>
      <a href="#" class="pagination-link" data-page="<?= $page - 1 ?>">&lt;</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="#" class="pagination-link <?= $i == $page ? 'active' : '' ?>" data-page="<?= $i ?>">
        <?= $i ?>
      </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
      <a href="#" class="pagination-link" data-page="<?= $page + 1 ?>">&gt;</a>
    <?php endif; ?>
  </div>
<?php endif; ?>
