<?php
/**
 * @var list<array{id: int, name: string}>   $products
 */
?>
<h1>List of Product(s)</h1>

<!-- toolbar: search on the left, add on the right; stacks on narrow screens -->
<div class="toolbar">
  <form method="GET" action="/products" class="inline-form"  id="filter-product">
    <label for="search_term" class="visually-hidden">filter products</label>
    <input type="search" placeholder="Search..." id="search_term" name="search_term"
           value="<?= e($_GET['search_term'] ?? '') ?>">
    <button type="submit">Filter Product(s)</button>
  </form>

  <a class="button" href="/products/create">New Product</a>

  <a class='button button-secondary' href="/">← Back Home</a>
</div>

<div class="cards">
  <?php foreach ($products as $product): ?>
    <a class="a-card" href="/products/<?=e($product["id"])?>">
      <div style="display: inline-flex; justify-content: center">
        <img
          style="object-fit: contain"
          class="product-image"
          width=256
          height=256
          loading='lazy'
          decoding="async"
          alt="Image of <?=e($product['name'])?>"
          src="/products/<?=e($product['id'])?>/image" />
      </div>
      <p style="font-size: 1.75ex"><?=e($product['name'])?></p>
    </a>
  <?php endforeach; ?>
</div>

<!--<div class="cards">-->
<!--  --><?php //foreach ($supermarkets as $supermarket): ?>
<!--    <a class="a-card" href="/supermarkets/--><?php //=e($supermarket["id"])?><!--">-->
<!--      <span>--><?php //=e($supermarket["name"])?><!--</span>-->
<!--    </a>-->
<!--  --><?php //endforeach; ?>
<!--</div>-->
