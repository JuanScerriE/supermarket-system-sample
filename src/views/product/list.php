<?php
/**
 * @var list<array{id: int, name: string}>   $products
 */

$search_term = (string) ($_GET['search_term'] ?? '');
?>
<div class="page-header">
  <h1>Products</h1>
  <div class="page-actions">
    <a class="button" href="/products/create">New Product</a>
  </div>
</div>

<div class="toolbar toolbar-sticky">
  <form method="GET" action="/products" class="inline-form" id="filter-product" role="search">
    <label for="search_term" class="visually-hidden">Search products</label>
    <input type="search" placeholder="Search products…" id="search_term" name="search_term"
           value="<?= e($search_term) ?>">
    <button type="submit">Search</button>
  </form>
</div>

<?php if ($products === []): ?>
  <p class="empty-state">
    <?= $search_term === '' ? 'No products yet.' : 'No products match "' . e($search_term) . '".' ?>
  </p>
<?php else: ?>
  <p class="count"><?= count($products) ?> product<?= count($products) === 1 ? '' : 's' ?></p>

  <ul class="cards">
    <?php foreach ($products as $product): ?>
      <li>
        <a class="card" href="/products/<?= e($product['id']) ?>">
          <img class="card-image product-image" width="256" height="256" loading="lazy" decoding="async"
               alt="" src="/products/<?= e($product['id']) ?>/image">
          <div class="card-body">
            <h2 class="card-title"><?= e($product['name']) ?></h2>
          </div>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
