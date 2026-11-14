<?php
/**
 * @var array{id: int, name: string}|null                                          $product
 */
?>

<h1>Product Detail(s)</h1>

<form style="display: inline-flex; flex-direction: column" method="POST" action="/products/<?=e($product["id"])?>" enctype="multipart/form-data" id="edit-product">
  <input type="hidden" name="_method" value="put" />
  <div style="display: flex; flex-direction: column; margin-bottom: 1em">
    <div style="display: inline-flex">
      <img
        style="object-fit: contain; border: 2px solid #111111; border-radius: var(--radius); margin-right: 1em; margin-bottom: 1em"
        id="product_preview"
        class="product-image"
        width=256
        height=256
        loading='lazy'
        decoding="async"
        alt="Image of <?=e($product['name'])?>"
        src="/products/<?=e($product['id'])?>/image" />
      <div style="display: inline-flex; flex-direction: column">
        <label style="margin-bottom: 1em" for="product_image_file">Upload/Change Product Image</label>
        <input type="file" id="product_image_file" name="product_image_file" accept='image/jpeg, image/png, image/webp'
               data-resize-max="512" data-preview="#product_preview" />
      </div>
    </div>
    <label style="margin-bottom: 1em" for="product_name">Product Name</label>
    <input type="text" id="product_name" name="product_name" style="font-size: 1.75ex" value="<?=e($product['name'])?>" />
  </div>
  <button class='button' type="submit">Edit Product</button>
</form>

<div class="toolbar">
  <form method="POST" action="/products/<?=e($product["id"])?>" class="inline-form" id="delete-product">
    <input type="hidden" name="_method" value="delete" />
    <button id="delete-product" class="button button-danger" type="submit">Delete Product</button>
  </form>

  <a class='button button-secondary' href="/products">← Back</a>
  <a class='button button-secondary' href="/">← Home</a>
</div>

<!--<p style="font-size: 1.5em">Supermarket Name:  --><?php //=e($supermarket["name"])?><!--</p>-->
<!--<div class="toolbar">-->
<!--  <a class='button' href="/supermarkets/--><?php //=e($supermarket["id"])?><!--/edit">Edit Supermarket Name</a>-->
<!---->
<!--  <form method="POST" action="/supermarkets/--><?php //=e($supermarket["id"])?><!--" class="inline-form" id="delete-supermarket">-->
<!--    <input type="hidden" name="_method" value="delete" />-->
<!--    <button id="delete-supermarket" class="button button-danger" type="submit">Delete Supermarket</button>-->
<!--  </form>-->
<!---->
<!--  <form method="GET" class="inline-form" id="products-search">-->
<!--      <label for="search_term" hidden aria-hidden="false">filter products</label>-->
<!--      <input style="width: 50%; margin: 1em 0" type="text" placeholder="Search..." id="search_term" name="search_term"-->
<!--             value="--><?php //= e($_GET['search_term'] ?? '') ?><!--">-->
<!--      <button class="form-button" type="submit">Filter Product(s)</button>-->
<!--  </form>-->
<!---->
<!--  <a class='button button-secondary' href="/">← Back Home</a>-->
<!--</div>-->
<!---->

<script>
  const product_images = document.querySelectorAll(".product-image");

  product_images.forEach((img) => {
    img.addEventListener("error", function(event){
      event.target.src = "/assets/no_image_available.jpg";
    });
  })
</script>

<!-- shrinks the picked image in the browser before upload (see the file for details) -->
<script src="/js/image-resize.js" defer></script>
