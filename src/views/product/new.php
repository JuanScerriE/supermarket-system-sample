<?php
?>

<h1>New Product Detail(s)</h1>

<form style="display: inline-flex; flex-direction: column" method="POST" action="/api/products/create" enctype="multipart/form-data" id="create-product">
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
        alt="Image of Product"
        src="/assets/no_image_available.jpg" />
      <div style="display: inline-flex; flex-direction: column">
        <label style="margin-bottom: 1em" for="product_image_file">Upload/Change Product Image</label>
        <input type="file" id="product_image_file" name="product_image_file" accept='image/jpeg, image/png, image/webp'
               data-resize-max="512" data-preview="#product_preview" />
      </div>
    </div>
    <label style="margin-bottom: 1em" for="product_name">Product Name</label>
    <input type="text" id="product_name" name="product_name" style="font-size: 1.75ex" placeholder="Product..." />
  </div>
  <button class='button' type="submit">Create Product</button>
</form>

<div class="toolbar">
  <a class='button button-secondary' href="/products">← Back</a>
  <a class='button button-secondary' href="/">← Home</a>
</div>

<!-- shrinks the picked image in the browser before upload (see the file for details) -->
<script src="/js/image-resize.js" defer></script>
