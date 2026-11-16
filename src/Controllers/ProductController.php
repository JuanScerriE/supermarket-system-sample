<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database;
use App\Models\Product;
use App\Models\Supermarket;
use App\Models\SupermarketProduct;
use finfo;
use PDOException;
use Throwable;

class ProductController
{
  // GET /products/create
//  public function create_product_view(): void
//  {
//    view('product/new', [
//      'title' => 'New Product',
//    ]);
//  }

  // POST /api/products/create
//  public function create_product(): void
//  {
//    if (empty($_POST['product_name'])) {
//      abort(400, 'expected product name');
//    }
//
//    $image_str = $this->read_uploaded_image();
//
//    // TODO(juan): test if this can be used for SQL injection
//    $name = $_POST['product_name'];
//
//    $id = Product::create($name, $image_str);
//
//    header("Location: /products/$id", true, 303);   // Post/Redirect/Get
//
//    exit;
//  }

  // GET /supermarkets/{supermarket_id}/products/{product_id}
//  public function supermarket_product_by_id(int $supermarket_id, int $product_id): void
//  {
//    $product = SupermarketProduct::get_by_id($supermarket_id, $product_id);
//
//    if (empty($product)) {
//      abort(404, 'the combination of supermarket with ID ' . $supermarket_id . ' and product with ID ' . $product_id . ' does not exist!');
//    }
//
//    view('supermarket_product/single', [
//      'title' => 'View/Edit Supermarket Product',
//      'product' => $product,
//    ]);
//  }

  // GET /supermarkets/{supermarket_id}/products/create
//  public function create_supermarket_product_view(int $supermarket_id): void
//  {
//    $supermarket = Supermarket::find_by_id($supermarket_id);
//
//    if ($supermarket === null) {
//      abort(404, 'supermarket with ID ' . $supermarket_id . ' does not exist!');
//    }
//
//    view('supermarket_product/new', [
//      'title'       => 'Add Product to ' . $supermarket['name'],
//      'supermarket' => $supermarket,
//      // only offer products this supermarket doesn't stock yet
////      'products'    => Product::get_not_in_supermarket($supermarket_id),
//    ]);
//  }

  // POST /api/supermarkets/{supermarket_id}/products/create
  // either links an existing product, or creates a new product and links it
//  public function create_supermarket_product(int $supermarket_id): void
//  {
//    $supermarket = Supermarket::find_by_id($supermarket_id);
//
//    if ($supermarket === null) {
//      abort(404, 'supermarket with ID ' . $supermarket_id . ' does not exist!');
//    }
//
//    $stock = $this->read_stock();
//    $price_cents = $this->read_price();
////    [$stock, $price_cents] = $this->read_stock_and_price();
//
//    $mode = $_POST['product_mode'] ?? '';
//
//    if ($mode === 'existing') {
//      // the picker sends the option text, e.g. "Coca Cola 250ml (#42)";
//      // product names aren't unique, so the id at the end is what counts
//      $picked = trim((string) ($_POST['existing_product'] ?? ''));
//
//      // TODO(juan): this is not a great way of going about it but
//      // works when considering the constraints caused by datalist, SSR
//      // and fetch not being very amenable to SSR
//      if (!preg_match('/\(#(\d+)\)$/', $picked, $matches)) {
//        abort(400, 'pick a product from the list');
//      }
//
//      $product_id = (int) $matches[1];
//
//      if (Product::find_by_id($product_id) === null) {
//        abort(404, 'product with ID ' . $product_id . ' does not exist!');
//      }
//
//      // the (supermarket_id, product_id) pair is the table's PRIMARY KEY, so
//      // a second INSERT would fail; give a clear message instead
//      if (SupermarketProduct::get_by_id($supermarket_id, $product_id) !== null) {
//        abort(409, '"' . $supermarket['name'] . '" already stocks this product');
//      }
//
//      SupermarketProduct::create($supermarket_id, $product_id, $stock, $price_cents);
//    } elseif ($mode === 'new') {
//      $name = trim((string) ($_POST['product_name'] ?? ''));
//
//      if ($name === '') {
//        abort(400, 'expected a name for the new product');
//      }
//
//      // checked before touching the database, so a bad image changes nothing
//      $image = $this->read_uploaded_image();
//
//      // TODO(juan): this is not the right way of doing this and we'll try to organise it later
//
//      // two INSERTs that must succeed or fail together: without a transaction,
//      // a failure in the second would leave a product that no supermarket stocks
//      $pdo = Database::connection();
//      $pdo->beginTransaction();
//
//      try {
//        $product_id = Product::create($name, $image);
//        SupermarketProduct::create($supermarket_id, $product_id, $stock, $price_cents);
//        $pdo->commit();
//      } catch (Throwable $e) {
//        $pdo->rollBack();
//        throw $e;
//      }
//    } else {
//      abort(400, 'choose either an existing product or a new product');
//    }
//
//    header("Location: /supermarkets/$supermarket_id/products/$product_id", true, 303);   // Post/Redirect/Get
//    exit;
//  }

  // PUT /supermarkets/{supermarket_id}/products/{product_id}
//  public function update_supermarket_product(int $supermarket_id, int $product_id): void
//  {
//
//    // TODO(juan): can we add an exists function
//    if (Product::find_by_id($product_id) === null) {
//      abort(404, 'product with internal ID ' . $product_id . ' does not exist!');
//    }
//
//    $name = trim((string) ($_POST['product_name']));
//
//    if (empty($name)) {
//      abort(400, 'expected product name');
//    }
//
//    $image_str = $this->read_uploaded_image();
//    $stock = $this->read_stock();
//    $price_cents = $this->read_price();
//
//
//    if ($image_str !== null && !Product::update_image($product_id, $image_str)) {
//      abort(500, 'failed to update image');
//    }
//
//    if (!SupermarketProduct::update($supermarket_id, $product_id, $name, $stock, $price_cents)) {
//      abort(500, 'failed to update supermarket with internal ID ' . $supermarket_id . ' and product with internal ID ' . $product_id);
//    }
//
//    // back to the supermarket's list (with the same search, if there was one)
//    // rather than staying on this page, so staff can go straight to the next
//    // product. #product-{id} scrolls to the card just edited and highlights it
//    $return_search = trim((string) ($_POST['return_search'] ?? ''));
//    $query = $return_search === '' ? '' : '?' . http_build_query(['search_term' => $return_search]);
//
//    // the target #product-$product_id allows us to keep track of the product when we go back
//    // and keep it highlighted.
//    header("Location: /supermarkets/$supermarket_id$query#product-$product_id", true, 303);   // Post/Redirect/Get
//    exit;
//  }

  // DELETE /supermarkets/{supermarket_id}/products/{product_id}
//  public function delete_supermarket_product(int $supermarket_id, int $product_id): void
//  {
//    // fetch first: once deleted, the names are gone and the page can't show them
//    $product = SupermarketProduct::get_by_id($supermarket_id, $product_id);
//
//    if ($product === null) {
//      abort(404, 'the combination of supermarket with ID ' . $supermarket_id . ' and product with ID ' . $product_id . ' does not exist!');
//    }
//
//    if (!SupermarketProduct::delete($product_id)) {
//      abort(500, 'could not remove product from supermarket');
//    }
//
//    view('supermarket_product/delete', [
//      'title'   => 'Removed Supermarket Product',
//      'product' => $product,
//    ]);
//  }

  // GET /products/{id}/image
//  public function product_image(int $id): void
//  {
//    $image_info = Product::get_image($id);
//
//    // no image stored for this product (or no such product): send the browser
//    // to the placeholder instead. 302 = temporary, so if the product gets an
//    // image later the browser asks again rather than remembering the redirect
//    if ($image_info === null || $image_info['image'] === null) {
//      header('Location: /assets/no_image_available.jpg', true, 302);
//      return;
//    }
//
//    // work out the real type from the bytes (jpeg/png/webp) instead of guessing
//    $mime = new finfo(FILEINFO_MIME_TYPE)->buffer($image_info['image']);
//
//    // let the browser skip the download if it already has this exact image
//    $etag = '"' . $image_info['image_md5'] . '"';
//    header('ETag: ' . $etag);
//    header('Cache-Control: no-cache');   // revalidate with the server
//    // behaviour is described here https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/If-None-Match
//    if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
//      http_response_code(304);   // "not modified", no body sent
//      return;
//    }
//
//    header('Content-Type: ' . $mime);
//    header('Content-Length: ' . strlen($image_info["image"]));
//    echo $image_info["image"];
//  }

  // DELETE /products/{id}
//  public function delete_product(int $id): void {
//    $product = Product::find_by_id($id);
//
//    if (empty($product)) {
//      abort(404, 'product does not exist');
//    }
//
//    if (!Product::delete($id)) {
//      abort(500, 'could not delete product');
//    }
//
//    view("product/delete", [
//      'title'  => 'Delete Product',
//      'product' => $product,
//    ]);
//  }

//  private function read_stock(): int {
//    // stock: a whole number, 0 or more. filter_var gives false for "abc",
//    // "1.5", "-3" or a missing field
//    $stock = filter_var($_POST['product_stock'] ?? null, FILTER_VALIDATE_INT, [
//      'options' => ['min_range' => 0],
//    ]);
//
//    if ($stock === false) {
//      abort(400, 'stock must be a whole number, 0 or more');
//    }
//
//    return intval($stock);
//  }
//
//  private function read_price(): int {
//    // price: euros as typed, e.g. "2" or "1,99". accept a comma too,
//    // since that is how many people in Europe write decimals
//    $price =  trim((string) ($_POST['product_price'] ?? ''));
//
//    if (!preg_match('/^\d+(\.\d{2})?$/', $price)) {
//      abort(400, 'price must be a number or a number with 2 decimals, e.g. 1 or 1.99');
//    }
//
//    // avoid any floating points by rounding
//    return (int) round(floatval($price) * 100);
//  }

  /**
   * read an uploaded image from $_FILES, check it, and shrink it to at most
   * 512px. stops with an error page (abort) if the upload is invalid.
   *
   *
   * @return string|null the image bytes, ready to store; null if no file was chosen
   */
//  private function read_uploaded_image(): ?string
//  {
//    $image_str = null;
//
//    if (!empty($_FILES['product_image_file'])
//      && $_FILES['product_image_file']["error"] !== UPLOAD_ERR_NO_FILE) {
//      $image = $_FILES['product_image_file'];
//
//      $image_error = $image["error"];
//
//      if ($image_error !== UPLOAD_ERR_OK) {
//        if ($image_error === UPLOAD_ERR_FORM_SIZE || $image['error'] === UPLOAD_ERR_INI_SIZE) {
//          abort(413, 'product image file exceed size limit');
//        }
//
//        abort(400, 'product image file upload failed');
//      }
//
//      $image_type = new finfo(FILEINFO_MIME_TYPE)->file($image['tmp_name']);
//
//      if ($image_type !== 'image/jpeg'
//        && $image_type !== 'image/png'
//        && $image_type !== 'image/webp') {
//        abort(415, 'only jpeg, png or webp files are allowed');
//      }
//
//      $gd_image = null;
//
//      switch ($image_type) {
//        case 'image/jpeg':
//          $gd_image = imagecreatefromjpeg($image['tmp_name']);
//          break;
//        case 'image/png':
//          $gd_image = imagecreatefrompng($image['tmp_name']);
//          break;
//        case 'image/webp':
//          $gd_image = imagecreatefromwebp($image['tmp_name']);
//          break;
//        default:
//          abort(500, 'unreachable; invalid image type');
//      }
//
//      if ($gd_image === false) {
//        abort(415, 'the image file is damaged or could not be read');
//      }
//
//      $width = imagesx($gd_image);
//      $height = imagesy($gd_image);
//      error_log("Image File Size: $width x $height");
//
//      $scaled_image = $gd_image;
//
//      // if the image file exceeds a specific size we need to resize the image
//      if ($width > 512 || $height > 512) {
//        if ($width > $height) {
//          $scaled_image = imagescale($gd_image, 512);
//        } else {
//          // intdiv: imagescale() needs an int, and with strict_types a float
//          // like 384.5 would throw a TypeError for most portrait photos
//          $scaled_image = imagescale($gd_image, intdiv($width * 512, $height));
//        }
//      }
//
//      ob_start();
//      switch ($image_type) {
//        case 'image/jpeg':
//          imagejpeg($scaled_image);
//          break;
//        case 'image/png':
//          imagepng($scaled_image);
//          break;
//        case 'image/webp':
//          imagewebp($scaled_image);
//          break;
//        default:
//          abort(500, 'unreachable; invalid image type');
//      }
//      $image_str = ob_get_clean();
//    }
//
//    return $image_str;
//  }
}
