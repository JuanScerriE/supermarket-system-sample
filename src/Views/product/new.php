<?php

/**
 * @var array{id: int, name: string}         $supermarket
 */

?>
<?= breadcrumbs([
  'Supermarkets'       => '/supermarkets',
  $supermarket['name'] => '/supermarkets/' . $supermarket['id'],
  'Add product'        => null,
]) ?>

<h1>Add Product to <?= e($supermarket['name']) ?></h1>

<form method="POST" action="/api/supermarkets/<?= e($supermarket['id']) ?>/products/create"
      enctype="multipart/form-data" class="form-stack panel" id="add-supermarket-product">
  <label for="product_name">Product name</label>
  <input type="text" id="product_name" name="product_name" placeholder="e.g. Coca Cola 250ml" required>

  <label for="product_image_file">Image (optional)</label>
  <div class="image-picker">
    <img id="product_preview" class="product-image" width="128" height="128"
         alt="" src="/assets/no_image_available.jpg">
    <div class="image-toolbar">
      <input type="file" id="product_image_file" name="product_image_file"
           accept="image/jpeg, image/png, image/webp"
           data-resize-max="512" data-preview="#product_preview">
      <button type="button" id="take-photo">
        Use camera
      </button>
    </div>
  </div>

  <label for="product_stock">Stock</label>
  <div class="stepper">
    <button type="button" id="decrease-button" class="button button-secondary" aria-label="Decrease by 1">-</button>
    <input type="number" id="product_stock" name="product_stock" min="0" step="1" inputmode="numeric"
           value="0" required>
    <button type="button" id="increase-button" class="button button-secondary" aria-label="Increase by 1">+</button>
  </div>

  <label for="product_price">Price</label>
  <div class="price-input">
    <span aria-hidden="true">€</span>
    <!-- inputmode gives a number keypad; pattern matches what the server accepts -->
    <input type="text" id="product_price" name="product_price" inputmode="decimal"
           pattern="\d+([.]\d{2})?" title="A number, or a number with 2 decimals, e.g. 1 or 1.99"
           placeholder="1.99" required>
  </div>

  <button type="submit">Add Product</button>
</form>

<dialog id="photo-modal" class="photo-modal">
  <video class="camera-view" id="camera" playsinline muted></video>
  <div class="toolbar">
    <button class="button button-danger" id="photo-modal-close">Close</button>
    <button type="button" id="camera-snap">Take Photo</button>
  </div>
</dialog>
