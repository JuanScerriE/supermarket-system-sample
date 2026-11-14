<?php
/**
 * @var array{supermarket_id: int, supermarket_name: string, product_id: int, product_name: string, stock: int, price_cents: int}|null  $product
 */
?>

<h1>Supermarket Product Detail(s)</h1>

<form style="display: inline-flex; flex-direction: column; width: 33vw" method="POST" action="/supermarkets/<?=e($product["supermarket_id"])?>/products/<?=e($product["product_id"])?>" enctype="multipart/form-data" id="edit-supermarket-product">
  <input type="hidden" name="_method" value="put" />
  <div style="display: flex; flex-direction: column; margin-bottom: 1em">
    <div style="display: inline-flex">
      <img
        style="object-fit: contain; border: 2px solid #111111; border-radius: var(--radius); margin-right: 1em; margin-bottom: 1em"
        class="product-image"
        width=256
        height=256
        loading='lazy'
        decoding="async"
        alt="Image of <?=e($product['product_name'])?>"
        src="/products/<?=e($product['product_id'])?>/image" />
      <div style="display: inline-flex; flex-direction: column">
<!--        <label style="margin-bottom: 1em" for="product_image_file">Upload/Change Product Image</label>-->
<!--        <input type="file" id="product_image_file" name="product_image_file" accept='image/jpeg, image/png, image/webp' alt="Image File of --><?php //=e($product['product_name'])?><!--" />-->

<!--        <label style="margin-bottom: 0.5em" for="product_name">Change Product</label>-->
<!--        <input class="input" list="product_options" style="font-size: 1.75ex; margin-bottom: 0.7em" id="product_name" autocomplete="on" />-->
<!--        <datalist id="product_options">-->
<!--          --><?php //foreach ($all_products as $option): ?>
<!--            <option value="--><?php //= e($option['name']) ?><!--" data-id="--><?php //= e($option['id']) ?><!--"></option>-->
<!--          --><?php //endforeach; ?>
<!--        </datalist>-->
<!--        <input type="hidden" hidden name="product_id" id="product_id">-->
      </div>
    </div>

    <label style="margin-bottom: 0.5em" for="old_product_name">Product Name</label>
    <input style="font-size: 1.75ex; margin-bottom: 0.7em" type="text" id="old_product_name" name="old_product_name" disabled value="<?=e($product['product_name'])?>" />

    <label style="margin-bottom: 0.5em" for="product_stock">Product Stock</label>
    <input style="font-size: 1.75ex; margin-bottom: 0.7em" type="number" id="product_stock" name="product_stock" value="<?=e($product['stock'])?>" />
    <label style="margin-bottom: 0.5em" for="product_price">Product Price</label>
    <input style="font-size: 1.75ex; margin-bottom: 0.7em" type="text" id="product_price" name="product_price" value="<?=from_cents_to_euro($product['price_cents'])?>" inputmode="decimal" />
  </div>
  <button class='button' type="submit">Edit Product</button>
</form>

<div class="toolbar">
  <form method="POST" action="/supermarkets/<?=e($product["supermarket_id"])?>/products/<?=e($product["product_id"])?>" class="inline-form" id="delete-product">
    <input type="hidden" name="_method" value="delete" />
    <button id="delete-product" class="button button-danger" type="submit">Delete Product</button>
  </form>

  <a class='button button-secondary' href="/supermarkets/<?=e($product["supermarket_id"])?>">← Back</a>
  <a class='button button-secondary' href="/">← Home</a>
</div>

<script>
  const product_images = document.querySelectorAll(".product-image");

  product_images.forEach((img) => {
    img.addEventListener("error", function(event){
      event.target.src = "/assets/no_image_available.jpg";
    });
  })
</script>
