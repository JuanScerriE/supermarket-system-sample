<?php
///**
// * add a product to a supermarket: either pick an existing product, or create
// * a new one. stock and price apply to both.
// *
// * @var array{id: int, name: string}         $supermarket
// * @var list<array{id: int, name: string}>   $products     products this supermarket doesn't stock yet
// */


/**
 * add a product to a supermarket: either pick an existing product, or create
 * a new one. stock and price apply to both.
 *
 * @var array{id: int, name: string}         $supermarket
 * @var list<array{id: int, name: string}>   $products     products this supermarket doesn't stock yet
 */

// with nothing left to pick, start on "new product"
//$default_mode = $products === [] ? 'new' : 'existing';
?>
<?= breadcrumbs([
  'Supermarkets'       => '/supermarkets',
  $supermarket['name'] => '/supermarkets/' . $supermarket['id'],
  'Add product'        => null,
]) ?>

<h1>Add Product to <?= e($supermarket['name']) ?></h1>

<form method="POST" action="/api/supermarkets/<?= e($supermarket['id']) ?>/products/create"
      enctype="multipart/form-data" class="form-stack panel" id="add-supermarket-product">

<!--  <fieldset class="choice">-->
<!--    <legend>Product</legend>-->
<!--    <label>-->
<!--      <input type="radio" name="product_mode" value="existing"-->
<!--        --><?php //= $default_mode === 'existing' ? 'checked' : '' ?>
<!--        --><?php //= $products === [] ? 'disabled' : '' ?><!--
      Use an existing product-->
<!--    </label>-->
<!--    <label>-->
<!--      <input type="radio" name="product_mode" value="new" --><?php //= $default_mode === 'new' ? 'checked' : '' ?><!--
      Create a new product-->
<!--    </label>-->
<!--  </fieldset>-->

  <!-- the section that isn't chosen is `disabled`: its fields are skipped
       by validation and not sent with the form -->
<!--  <fieldset id="existing-product-fields" --><?php //= $default_mode !== 'existing' ? 'disabled' : '' ?><!--
    <label for="existing_product">Search products</label>-->
<!--    <input type="text" list="product_options" id="existing_product" name="existing_product"-->
<!--           placeholder="Start typing…" autocomplete="off" required>-->
<!--    <datalist id="product_options">-->
<!--      --><?php //foreach ($products as $product): ?>
<!--        <option data-product-id="--><?php //= e($product['id']) ?><!--" value="--><?php //= e($product['name']) ?><!-- (#--><?php //= e($product['id']) ?><!--)"></option>-->
<!--      --><?php //endforeach; ?>
<!--    </datalist>-->
<!--    --><?php //if ($products === []): ?>
<!--      <p class="hint">--><?php //= e($supermarket['name']) ?><!-- already stocks every product.</p>-->
<!--    --><?php //endif; ?>
<!--  </fieldset>-->

    <!-- TODO(juan): this is only meant for actually switching on the backend which needs to be updated. -->
    <input type="hidden" name="product_mode" value="new">

<!--  <fieldset id="new-product-fields" --><?php //= $default_mode !== 'new' ? 'disabled' : '' ?>
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
<!--  </fieldset>-->

  <label for="product_stock">Stock</label>
  <input type="number" id="product_stock" name="product_stock" min="0" step="1" inputmode="numeric"
         value="0" required data-stepper>

  <label for="product_price">Price</label>
  <div class="price-input">
    <span aria-hidden="true">€</span>
    <!-- inputmode gives a number keypad; pattern matches what the server accepts -->
    <input type="text" id="product_price" name="product_price" inputmode="decimal"
           pattern="\d+([.,]\d{2})?" title="A number, or a number with 2 decimals, e.g. 1 or 1.99"
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

<script>
  // const form = document.querySelector('#add-supermarket-product');
  //
  // const sections = {
  //   existing: document.querySelector('#existing-product-fields'),
  //   new: document.querySelector('#new-product-fields'),
  // };

  // switching the radio enables one section and disables the other
  // (CSS hides the disabled one, see .form-stack fieldset:disabled)
  // form.addEventListener('change', (event) => {
  //   if (event.target.name !== 'product_mode') return;
  //
  //   for (const [mode, section] of Object.entries(sections)) {
  //     section.disabled = mode !== event.target.value;
  //   }
  // });

  // const picker = document.querySelector('#existing_product');

  // TODO(juan): this is not a good way of doing things honestly
  // form.addEventListener('submit', (event) => {
  //   const option = [...picker.list.options].find((option) => {
  //     return option.value === picker.value;
  //   });
  //
  //   // get the value of the product_id of the selected value instead of the full name
  //   picker.value = option.dataset.productId;
  // });

  // picker.addEventListener('input', () => {
  //   const match = [...picker.list.options].some((option) => option.value === picker.value);
  //   picker.setCustomValidity(match ? '' : 'Pick a product from the list.');
  // });
</script>
