<?php
/**
 * @var array{id: int, name: string}|null                                          $supermarket
 * @var list<array{id: int, name: string, stock: int, price_cents: int}>           $products
 */
?>

<h1>Supermarket Detail(s)</h1>

<p style="font-size: 1.5em">Supermarket Name:  <?=e($supermarket["name"])?></p>
<div class="toolbar">
  <a class='button' href="/supermarkets/<?=e($supermarket["id"])?>/edit">Edit Supermarket Name</a>

  <form method="POST" action="/supermarkets/<?=e($supermarket["id"])?>" class="inline-form" id="delete-supermarket">
    <input type="hidden" name="_method" value="delete" />
    <button id="delete-supermarket" class="button button-danger" type="submit">Delete Supermarket</button>
  </form>
</div>
<div class="toolbar">
  <form method="GET" class="inline-form" id="products-search">
      <label for="search_term" class="visually-hidden">filter products</label>
      <input type="search" placeholder="Search..." id="search_term" name="search_term"
             value="<?= e($_GET['search_term'] ?? '') ?>">
      <button type="submit">Filter Product(s)</button>
  </form>

  <a class="button" href="/supermarkets/<?=e($supermarket["id"])?>/products/create">Add Product</a>
</div>
<div class="toolbar">
  <a class='button button-secondary' href="/supermarkets">← Back</a>
  <a class='button button-secondary' href="/">← Home</a>
</div>

<?php if (empty($products)): ?>
  <p>this supermarket has no products.</p>
<?php else: ?>
  <p><?= count($products) ?> products</p>

  <div class="cards">
    <?php foreach ($products as $product): ?>
      <a class="a-card" href="/supermarkets/<?=e($supermarket["id"])?>/products/<?=e($product["id"])?>">
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
        <p style="font-size: 1.5ex">Product Stock: <?=e($product['stock'])?></p>
        <p style="font-size: 1.5ex">Product Price: <?=from_cents_to_euro($product["price_cents"])?></p>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<script>
  const product_images = document.querySelectorAll(".product-image");

  product_images.forEach((img) => {
    img.addEventListener("error", function(event){
      event.target.src = "/assets/no_image_available.jpg";
    });
  })

  // delete button on /supermarkets/{id}. the server answers the DELETE with a
  // full HTML page (the "deleted" page, or an error page from abort()), and we
  // swap the current page for it.
  // `?.` because most pages don't have this button
  // const delete_button = document.querySelector('#delete-supermarket');
  //
  // delete_button?.addEventListener('click', async () => {
  //   if (!confirm(`Delete "${delete_button.dataset.name}" and all its products?`)) {
  //     return;
  //   }

    // delete_button.disabled = true; // stop double clicks while the request runs
    //
    // let response;
    // try {
    //   response = await fetch(delete_button.dataset.url, { method: 'DELETE' });
    // } catch (error) {
    //   // only network failures land here (server down, offline); HTTP errors
    //   // like 404/500 still give a response, which we render below
    //   alert(`Could not reach the server: ${error.message}`);
    //   delete_button.disabled = false;
    //   return;
    // }
    //
    // const html = await response.text();
    //
    // if (response.ok) {
    //   // the supermarket is gone, so its URL would now 404. change the address
    //   // bar (without loading anything) so a refresh shows the list instead,
    //   // and never sends the DELETE again
    //   history.replaceState(null, '', '/supermarkets');
    // }
    //
    // // replace the whole document with the page the server rendered; its
    // // <title>, styles and scripts load as if we had navigated to it
    // document.open();
    // document.write(html);
    // document.close();
  // });
</script>
