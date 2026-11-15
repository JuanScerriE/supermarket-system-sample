<?php
/**
 * @var array{id: int, name: string}   $supermarket
 */
?>
<section class="panel message">
  <h1><?= e($supermarket['name']) ?> has been deleted</h1>
  <p class="hint">Its stock and prices were removed too. The products themselves still exist.</p>
  <div class="page-actions">
    <a class="button" href="/supermarkets">Back to supermarkets</a>
  </div>
</section>
