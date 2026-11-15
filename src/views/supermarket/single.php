<?php
/**
 * one supermarket and the products it stocks: the main screen for staff
 * updating stock and prices.
 *
 * @var array{id: int, name: string}                                       $supermarket
 * @var list<array{id: int, name: string, stock: int, price_cents: int}>   $products
 */

$search_term = (string) ($_GET['search_term'] ?? '');

// the edit page gets the search term too, so "Save" can bring staff back to
// this same filtered list (see ProductController::update_supermarket_product)
$query = $search_term === '' ? '' : '?' . http_build_query(['search_term' => $search_term]);
?>
<?= breadcrumbs(['Supermarkets' => '/supermarkets', $supermarket['name'] => null]) ?>

<div class="page-header">
  <h1><?= e($supermarket['name']) ?></h1>
  <div class="page-actions">
    <a class="button" href="/supermarkets/<?= e($supermarket['id']) ?>/products/create">Add Product</a>
    <a class="button button-secondary" href="/supermarkets/<?= e($supermarket['id']) ?>/edit">Rename</a>
  </div>
</div>

<div class="toolbar toolbar-sticky">
  <form method="GET" class="inline-form" id="products-search" role="search">
    <label for="search_term" class="visually-hidden">Search products</label>
    <input type="search" placeholder="Search products…" id="search_term" name="search_term"
           value="<?= e($search_term) ?>">
    <button type="submit">Search</button>
  </form>
</div>

<?php if ($products === []): ?>
  <p class="empty-state">
    <?= $search_term === ''
      ? e($supermarket['name']) . ' has no products yet.'
      : 'No products match "' . e($search_term) . '".' ?>
  </p>
<?php else: ?>
  <p class="count"><?= count($products) ?> product<?= count($products) === 1 ? '' : 's' ?></p>

  <ul class="cards">
    <?php foreach ($products as $product): ?>
      <li>
        <!-- id: after saving, the list scrolls back to (and highlights) this card -->
        <a class="card" id="product-<?= e($product['id']) ?>"
           href="/supermarkets/<?= e($supermarket['id']) ?>/products/<?= e($product['id']) ?><?= e($query) ?>">
          <img class="card-image product-image" width="256" height="256" loading="lazy" decoding="async"
               alt="" src="/products/<?= e($product['id']) ?>/image">
          <div class="card-body">
            <h2 class="card-title"><?= e($product['name']) ?></h2>
            <div class="card-meta">
              <span class="card-price"><?= format_price($product['price_cents']) ?></span>
              <?php if ($product['stock'] === 0): ?>
                <span class="badge badge-danger">Out of stock</span>
              <?php else: ?>
                <span class="badge"><?= e($product['stock']) ?> in stock</span>
              <?php endif; ?>
            </div>
          </div>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<section class="danger-zone">
  <h2>Delete supermarket</h2>
  <form method="POST" action="/supermarkets/<?= e($supermarket['id']) ?>" id="delete-supermarket"
        data-confirm="Delete <?= e($supermarket['name']) ?> and its stock and prices? This can't be undone.">
    <input type="hidden" name="_method" value="delete" />
    <button class="button button-danger" type="submit">Delete <?= e($supermarket['name']) ?></button>
  </form>
</section>
