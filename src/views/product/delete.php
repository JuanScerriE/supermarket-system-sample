<?php
/**
 * @var array{id: int, name: string}   $product
 */
?>
<section class="panel message">
  <h1><?= e($product['name']) ?> has been deleted</h1>
  <p class="hint">It was also removed from every supermarket that stocked it.</p>
  <div class="page-actions">
    <a class="button" href="/products">Back to products</a>
  </div>
</section>
