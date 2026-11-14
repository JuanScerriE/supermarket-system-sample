<?php
/**
 * @var array{supermarket_id: int, supermarket_name: string, product_id: int, product_name: string, stock: int, price_cents: int}  $product
 */
?>

<section>
  <h1><?= e($product['product_name']) ?> has been removed from <?= e($product['supermarket_name']) ?>!</h1>
  <p>the product itself still exists; <?= e($product['supermarket_name']) ?> just no longer stocks it.</p>
  <div class="toolbar">
    <a class='button button-secondary' href="/supermarkets/<?= e($product['supermarket_id']) ?>">← Back to <?= e($product['supermarket_name']) ?></a>
    <a class='button button-secondary' href="/">← Back Home</a>
  </div>
</section>
