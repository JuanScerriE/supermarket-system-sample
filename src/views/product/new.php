<?= breadcrumbs(['Products' => '/products', 'New product' => null]) ?>

<h1>New Product</h1>

<div class="detail">
  <img id="product_preview" class="detail-image product-image" width="280" height="280"
       alt="" src="/assets/no_image_available.jpg">

  <form method="POST" action="/api/products/create" enctype="multipart/form-data"
        class="form-stack panel" id="create-product">
    <label for="product_name">Product name</label>
    <input type="text" id="product_name" name="product_name" placeholder="e.g. Coca Cola Can 330ml" required />

    <label for="product_image_file">Image (optional)</label>
<!--    <input type="file" id="product_image_file" name="product_image_file" accept="image/jpeg, image/png, image/webp"-->
<!--           data-resize-max="512" data-preview="#product_preview" />-->
    <div class="image-toolbar">
      <input type="file" id="product_image_file" name="product_image_file"
             accept="image/jpeg, image/png, image/webp"
             data-resize-max="512" data-preview="#product_preview">
      <button type="button" id="take-photo">
        Use camera
      </button>
    </div>
    <p class="hint">JPEG, PNG or WebP. Large photos are shrunk before uploading.</p>

    <button type="submit">Create Product</button>
  </form>
</div>

<dialog id="photo-modal" class="photo-modal">
  <video class="camera-view" id="camera" playsinline muted></video>
  <div class="toolbar">
    <button class="button button-danger" id="photo-modal-close">Close</button>
    <button type="button" id="camera-snap">Take Photo</button>
  </div>
</dialog>
