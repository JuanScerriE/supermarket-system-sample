<?php
/**
 * @var array{id: int, name: string}   $supermarket
 */
?>
<section class="panel message">
  <h1><?= e($supermarket['name']) ?> has been deleted</h1>
  <p class="hint">Its products have also been deleted.</p>
  <div class="page-actions">
    <a class="button" href="/supermarkets">Back to supermarkets</a>
  </div>
</section>
