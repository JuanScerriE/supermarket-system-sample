<?php
/**
 * edit the stock and price of one product at one supermarket: the screen
 * staff use most, so big controls and one obvious "Save".
 *
 * @var array{supermarket_id: int, supermarket_name: string, product_id: int, product_name: string, stock: int, price_cents: int}  $product
 */

// the list's search term, passed along so "Save" returns to the same filtered list
$return_search = (string) ($_GET['search_term'] ?? '');

$supermarket_url = '/supermarkets/' . $product['supermarket_id']
  . ($return_search === '' ? '' : '?' . http_build_query(['search_term' => $return_search]));

$action = '/supermarkets/' . $product['supermarket_id'] . '/products/' . $product['product_id'];
?>
<?= breadcrumbs([
  'Supermarkets'               => '/supermarkets',
  $product['supermarket_name'] => $supermarket_url,
  $product['product_name']     => null,
]) ?>

<h1><?= e($product['product_name']) ?></h1>

<div class="detail">
  <img class="detail-image product-image" width="280" height="280"
       alt="Image of <?= e($product['product_name']) ?>" src="/products/<?= e($product['product_id']) ?>/image">

  <form method="POST" action="<?= e($action) ?>" class="form-stack panel" id="edit-supermarket-product">
    <input type="hidden" name="_method" value="put" />
    <input type="hidden" name="return_search" value="<?= e($return_search) ?>" />

    <p class="hint">At <?= e($product['supermarket_name']) ?></p>

    <label for="product_stock">Stock</label>
    <input type="number" id="product_stock" name="product_stock" min="0" step="1" inputmode="numeric"
           value="<?= e($product['stock']) ?>" required data-stepper />

    <label for="product_price">Price</label>
    <div class="price-input">
      <span aria-hidden="true">€</span>
      <!-- inputmode gives a number keypad; pattern matches what the server accepts -->
      <input type="text" id="product_price" name="product_price" inputmode="decimal"
             pattern="\d+([.,]\d{2})?" title="A number, or a number with 2 decimals, e.g. 1 or 1.99"
             value="<?= from_cents_to_euro($product['price_cents']) ?>" required />
    </div>

    <button type="submit">Save</button>
  </form>
</div>

<section class="danger-zone">
  <form method="POST" action="<?= e($action) ?>" id="delete-supermarket-product"
        data-confirm="Remove <?= e($product['product_name']) ?> from <?= e($product['supermarket_name']) ?>? The product itself is kept.">
    <input type="hidden" name="_method" value="delete" />
    <button class="button button-danger" type="submit">Remove product from supermarket</button>
  </form>
</section>
