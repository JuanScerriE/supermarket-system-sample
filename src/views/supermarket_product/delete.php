<?php
/**
 * @var array{supermarket_id: int, supermarket_name: string, product_id: int, product_name: string, stock: int, price_cents: int}  $product
 */
?>
<section class="panel message">
  <h1><?= e($product['product_name']) ?> removed from <?= e($product['supermarket_name']) ?></h1>
  <p class="hint">The product itself still exists; <?= e($product['supermarket_name']) ?> just no longer stocks it.</p>
  <div class="page-actions">
    <a class="button" href="/supermarkets/<?= e($product['supermarket_id']) ?>">Back to <?= e($product['supermarket_name']) ?></a>
  </div>
</section>
