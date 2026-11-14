<?php
/**
 * @var list<array{id: int, name: string}>   $supermarkets
 */
?>
<h1>List of Supermarket(s)</h1>

<!-- toolbar: search on the left, add on the right; stacks on narrow screens -->
<div class="toolbar">
  <form method="GET" action="/supermarkets" class="inline-form"  id="filter-supermarket">
    <label for="search_term" class="visually-hidden">filter supermarkets</label>
    <input type="search" placeholder="Search..." id="search_term" name="search_term"
           value="<?= e($_GET['search_term'] ?? '') ?>">
    <button type="submit">Filter Supermarket(s)</button>
  </form>

  <form method="POST" action="/api/supermarkets/create" class="inline-form" id="new-supermarket">
    <label for="supermarket_name" class="visually-hidden">new supermarket name</label>
    <input type="text" placeholder="Supermarket..." id="supermarket_name" name="supermarket_name" required>
    <button type="submit">Add Supermarket</button>
  </form>

  <a class='button button-secondary' href="/">← Back Home</a>
</div>

<div class="cards">
  <?php foreach ($supermarkets as $supermarket): ?>
    <a class="a-card" href="/supermarkets/<?=e($supermarket["id"])?>">
      <span><?=e($supermarket["name"])?></span>
    </a>
  <?php endforeach; ?>
</div>

<?php //if ($selected !== null): ?>
<!--  <h2>--><?php //= e($selected['name']) ?><!--</h2>-->
<!---->
<!--  --><?php //if ($products === []): ?>
<!--    <p>This supermarket has no products.</p>-->
<!--  --><?php //else: ?>
<!--    <p>--><?php //= count($products) ?><!-- products</p>-->
<!--    <table>-->
<!--      <thead>-->
<!--        <tr><th>Product</th><th>Stock</th><th>Price</th></tr>-->
<!--      </thead>-->
<!--      <tbody>-->
<!--        --><?php //foreach ($products as $product): ?>
<!--          <tr>-->
<!--            <td>--><?php //= e($product['name']) ?><!--</td>-->
<!--            <td>--><?php //= $product['stock'] === 0 ? 'Out of stock' : e($product['stock']) ?><!--</td>-->
<!--            <td>€--><?php //= e(number_format($product['price_cents'] / 100, 2)) ?><!--</td>-->
<!--          </tr>-->
<!--        --><?php //endforeach; ?>
<!--      </tbody>-->
<!--    </table>-->
<!--  --><?php //endif; ?>
<?php //endif; ?>
