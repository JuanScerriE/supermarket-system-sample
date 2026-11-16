<?php

/**
 * FRONT CONTROLLER — the single entry point for every request.
 *
 * Only the /public directory is exposed to the web server, so code in
 * /src, /Views, config.php and the database file can't be downloaded
 * directly. Every URL (e.g. /products/5) is sent to this file, and the
 * Router decides which controller method handles it.
 *
 * Request lifecycle:
 *   browser → index.php → bootstrap (config, autoload, session)
 *           → Router::dispatch() → Controller method
 *           → Model (database) → view (HTML) or JSON → browser
 */

declare(strict_types=1);

//// When using PHP's built-in dev server (`php -S`), let it serve real files
//// like /assets/style.css itself instead of routing them through PHP.
//if (PHP_SAPI === 'cli-server') {
//  $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
//  if (is_file($file)) {
//    return false;
//  }
//}

// load the config.php file after which just use the config function
$GLOBALS['config'] = require __DIR__ . '/../config.php';

define('BASE_PATH', $GLOBALS['config']['base_path']);

require BASE_PATH . '/vendor/autoload.php';

// ---- request body too large ----

$content_length = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);

$post_max_size = ini_parse_quantity(ini_get('post_max_size'));

if ($post_max_size > 0 && $content_length > $post_max_size) {
  abort(413, sprintf(
    // the likelihood of exceeding the post_max_size with just normal responses is quite remote
    // so we may assume that is due to a file upload
    'the upload is too large (%.1f MB). the maximum is %.0f MB; try a smaller image/file.',
    $content_length / 1024 / 1024,
    $post_max_size / 1024 / 1024,
  ));
}

use App\Router;
use App\Controllers\ProductController;
use App\Controllers\SupermarketController;

$router = new Router();

$router->get('/', [SupermarketController::class, 'home']);

// list of supermarkets
$router->get('/supermarkets', [SupermarketController::class, 'supermarkets']);
// specific supermarket
$router->get('/supermarkets/{id}', [SupermarketController::class, 'supermarket_by_id']);

$router->get('/supermarkets/{id}/edit', [SupermarketController::class, 'update_view']);
$router->put('/supermarkets/{id}', [SupermarketController::class, 'update']);
$router->get('/supermarkets/create', [SupermarketController::class, 'create_view']);
$router->post('/api/supermarkets/create', [SupermarketController::class, 'create']);
$router->delete('/supermarkets/{id}', [SupermarketController::class, 'delete']);

// for getting images
$router->get('/products/{id}/image', [ProductController::class, 'product_image']);

// view the product under the supermarket
$router->get('/supermarkets/{supermarket_id}/products/{product_id}', [ProductController::class, 'read']);

$router->put('/supermarkets/{supermarket_id}/products/{product_id}', [ProductController::class, 'update']);
$router->get('/supermarkets/{supermarket_id}/products/create', [ProductController::class, 'create_view']);
$router->post('/api/supermarkets/{supermarket_id}/products/create', [ProductController::class, 'create']);
$router->delete('/supermarkets/{supermarket_id}/products/{product_id}', [ProductController::class, 'delete']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

