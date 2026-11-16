<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Supermarket;
use App\Models\Product;
use finfo;

class ProductController
{
  // GET /supermarkets/{supermarket_id}/products/create
  public function create_view(int $supermarket_id): void
  {
    $supermarket = Supermarket::find_by_id($supermarket_id);

    if ($supermarket === null) {
      abort(404, 'supermarket with ID ' . $supermarket_id . ' does not exist!');
    }

    view('product/new', [
      'title'       => 'Add Product to ' . $supermarket['name'],
      'supermarket' => $supermarket,
    ]);
  }

  // POST /api/supermarkets/{supermarket_id}/products/create
  public function create(int $supermarket_id): void {
    $supermarket = Supermarket::find_by_id($supermarket_id);

    if ($supermarket === null) {
      abort(404, 'supermarket with ID ' . $supermarket_id . ' does not exist!');
    }

    $name = trim((string) ($_POST['product_name'] ?? ''));

    if ($name === '') {
      abort(400, 'expected product name');
    }

    $image_str = $this->read_uploaded_image();
    $stock = $this->read_stock();
    $price_cents = $this->read_price();

    $product_id = Product::create($supermarket_id, $name, $stock, $price_cents, $image_str);

    if (!$product_id) {
      abort(500, 'failed to create product ' . $name . ' in ' . $supermarket['name']);
    }

    header("Location: /supermarkets/$supermarket_id/products/$product_id", true, 303);   // Post/Redirect/Get
    exit;
  }

  // GET /supermarkets/{supermarket_id}/products/{product_id}
  public function read(int $supermarket_id, int $product_id): void {
    $product = Product::get_by_id($supermarket_id, $product_id);

    if ($product === null) {
      abort(404, 'the combination of supermarket with ID ' . $supermarket_id . ' and product with ID ' . $product_id . ' does not exist!');
    }

    view('product/single', [
      'title' => 'View/Edit Supermarket Product',
      'product' => $product,
    ]);
  }

  // PUT /supermarkets/{supermarket_id}/products/{product_id}
  public function update(int $supermarket_id, int $product_id): void
  {
    $product = Product::get_by_id($supermarket_id, $product_id);

    if ($product === null) {
      abort(404, 'the combination of supermarket with internal ID ' . $supermarket_id . ' and product with internal ID ' . $product_id . ' does not exist!');
    }

    $name = trim((string) ($_POST['product_name'] ?? ''));

    if ($name === '') {
      abort(400, 'expected product name');
    }

    $image_str = $this->read_uploaded_image();
    $stock = $this->read_stock();
    $price_cents = $this->read_price();

    // TODO: this can actually result in the image being updated but not the actual product
    if ($image_str !== null && !Product::update_image($product_id, $image_str)) {
      abort(500, 'failed to update image');
    }

    if (!Product::update($supermarket_id, $product_id, $name, $stock, $price_cents)) {
      abort(500, 'failed to update supermarket with internal ID ' . $supermarket_id . ' and product with internal ID ' . $product_id);
    }

    // back to the supermarket's list (with the same search, if there was one)
    // rather than staying on this page, so staff can go straight to the next
    // product. #product-{id} scrolls to the card just edited and highlights it
    $return_search = trim((string) ($_POST['return_search'] ?? ''));
    $query = $return_search === '' ? '' : '?' . http_build_query(['search_term' => $return_search]);

    // the target #product-$product_id allows us to keep track of the product when we go back
    // and keep it highlighted.
    header("Location: /supermarkets/$supermarket_id$query#product-$product_id", true, 303);   // Post/Redirect/Get
    exit;
  }

  // DELETE /supermarkets/{supermarket_id}/products/{product_id}
  public function delete(int $supermarket_id, int $product_id): void
  {
    $product = Product::get_by_id($supermarket_id, $product_id);

    if ($product === null) {
      abort(404, 'the combination of supermarket with ID ' . $supermarket_id . ' and product with ID ' . $product_id . ' does not exist!');
    }

    if (!Product::delete($product_id)) {
      abort(500, 'could not remove product from supermarket');
    }

    view('product/delete', [
      'title'   => 'Removed Supermarket Product',
      'product' => $product,
    ]);
  }

  // GET /products/{id}/image
  public function product_image(int $id): void
  {
    $image_info = Product::get_image($id);

    // no image stored for this product (or no such product): send the browser
    // to the placeholder instead. 302 = temporary, so if the product gets an
    // image later the browser asks again rather than remembering the redirect
    if ($image_info === null || $image_info['image'] === null) {
      header('Location: /assets/no_image_available.jpg', true, 302);
      return;
    }

    // work out the real type from the bytes (jpeg/png/webp) instead of guessing
    $mime = new finfo(FILEINFO_MIME_TYPE)->buffer($image_info['image']);

    // let the browser skip the download if it already has this exact image
    $etag = '"' . $image_info['image_md5'] . '"';
    header('ETag: ' . $etag);
    header('Cache-Control: no-cache');   // revalidate with the server
    // behaviour is described here https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/If-None-Match
    if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
      http_response_code(304);   // "not modified", no body sent
      return;
    }

    header('Content-Type: ' . $mime);
    header('Content-Length: ' . strlen($image_info["image"]));
    echo $image_info["image"];
  }

  private function read_stock(): int
  {
    $stock = trim((string) ($_POST['product_stock'] ?? ''));

    if (!preg_match('/^\d+$/', $stock)) {
      abort(400, 'stock must be a number, 0 or more');
    }

    return intval($stock);
  }

  private function read_price(): int
  {
    $price = trim((string) ($_POST['product_price'] ?? ''));

    if (!preg_match('/^\d+(\.\d{2})?$/', $price)) {
      abort(400, 'price must be a number or a number with 2 decimals, e.g. 1 or 1.99');
    }

    // avoid any floating points by rounding
    return (int)round(floatval($price) * 100);
  }

  /**
   * read an uploaded image from $_FILES, check it, and shrink it to at most
   * 512px. stops with an error page (abort) if the upload is invalid.
   *
   *
   * @return string|null the image bytes, ready to store; null if no file was chosen
   */
  private function read_uploaded_image(): ?string
  {
    $image_str = null;

    if (!empty($_FILES['product_image_file'])
      && $_FILES['product_image_file']["error"] !== UPLOAD_ERR_NO_FILE) {
      $image = $_FILES['product_image_file'];

      $image_error = $image["error"];

      if ($image_error !== UPLOAD_ERR_OK) {
        if ($image_error === UPLOAD_ERR_FORM_SIZE || $image['error'] === UPLOAD_ERR_INI_SIZE) {
          abort(413, 'product image file exceed size limit');
        }

        abort(400, 'product image file upload failed');
      }

      $image_type = new finfo(FILEINFO_MIME_TYPE)->file($image['tmp_name']);

      if ($image_type !== 'image/jpeg'
        && $image_type !== 'image/png'
        && $image_type !== 'image/webp') {
        abort(415, 'only jpeg, png or webp files are allowed');
      }

      $size = getimagesize($image['tmp_name']);

      // NOTE: we are doing this to avoid loading very large images into memory
      // potentially crashing the server
      if ($size[0] > 4000 || $size[1] > 4000) {
        abort(415, 'product image file due to extremely large file');
      }

      $gd_image = null;

      switch ($image_type) {
        case 'image/jpeg':
          $gd_image = imagecreatefromjpeg($image['tmp_name']);
          break;
        case 'image/png':
          $gd_image = imagecreatefrompng($image['tmp_name']);
          break;
        case 'image/webp':
          $gd_image = imagecreatefromwebp($image['tmp_name']);
          break;
        default:
          abort(500, 'unreachable; invalid image type');
      }

      if ($gd_image === false) {
        abort(415, 'the image file is damaged or could not be read');
      }

      $width = imagesx($gd_image);
      $height = imagesy($gd_image);
      error_log("Image File Size: $width x $height");

      $scaled_image = $gd_image;

      // if the image file exceeds a specific size we need to resize the image
      if ($width > 512 || $height > 512) {
        if ($width > $height) {
          $scaled_image = imagescale($gd_image, 512);
        } else {
          // intdiv: imagescale() needs an int, and with strict_types a float
          // like 384.5 would throw a TypeError for most portrait photos
          $scaled_image = imagescale($gd_image, intdiv($width * 512, $height));
        }
      }

      ob_start();
      switch ($image_type) {
        case 'image/jpeg':
          imagejpeg($scaled_image);
          break;
        case 'image/png':
          imagepng($scaled_image);
          break;
        case 'image/webp':
          imagewebp($scaled_image);
          break;
        default:
          abort(500, 'unreachable; invalid image type');
      }
      $image_str = ob_get_clean();
    }

    return $image_str;
  }
}
