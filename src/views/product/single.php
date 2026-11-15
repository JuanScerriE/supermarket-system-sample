<?php
/**
 * view / edit one product (its name and image, shared by every supermarket)
 *
 * @var array{id: int, name: string}   $product
 */
?>
<?= breadcrumbs(['Products' => '/products', $product['name'] => null]) ?>

<h1><?= e($product['name']) ?></h1>

<div class="detail">
  <img id="product_preview" class="detail-image product-image" width="256" height="256"
       alt="Image of <?= e($product['name']) ?>" src="/products/<?= e($product['id']) ?>/image">

  <form method="POST" action="/products/<?= e($product['id']) ?>" enctype="multipart/form-data"
        class="form-stack panel" id="edit-product">
    <input type="hidden" name="_method" value="put" />

    <label for="product_name">Product name</label>
    <input type="text" id="product_name" name="product_name" value="<?= e($product['name']) ?>" required />

    <label for="product_image_file">Change image</label>
    <input type="file" id="product_image_file" name="product_image_file" accept="image/jpeg, image/png, image/webp"
           data-resize-max="512" data-preview="#product_preview" />
    <p class="hint">JPEG, PNG or WebP. Large photos are shrunk before uploading.</p>

    <button type="submit">Save</button>
  </form>
</div>

<section class="danger-zone">
  <form method="POST" action="/products/<?= e($product['id']) ?>" id="delete-product"
        data-confirm="Delete <?= e($product['name']) ?>? It will be removed from every supermarket too.">
    <input type="hidden" name="_method" value="delete" />
    <button class="button button-danger" type="submit">Delete product</button>
  </form>
</section>
