

<?php if (!empty($products)): ?>
  <?php foreach ($products as $product): ?>
    <?php include __DIR__ . '/_ProductCard.php'; ?>
  <?php endforeach; ?>
<?php else: ?>
  <div class="no-products">Không tìm thấy sản phẩm nào</div>
<?php endif; ?>
