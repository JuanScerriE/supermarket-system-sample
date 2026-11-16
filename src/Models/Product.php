<?php

declare(strict_types=1);

namespace App\Models;

use App\Database;
use PDO;
use Throwable;

class Product {

  /**
   * create a new product entry
   *
   * @param int $supermarket_id id of the supermarket
   * @param string $name the name of the product
   * @param int $stock the amount of product in stock
   * @param int $price_cents the price of the product in cents (e.g. 1 euro => 100 cents)
   * @param string|null $image_str
   *
   * @return int|null returns last inserted id
   *
   * @throws Throwable
   */

  public static function create(int $supermarket_id, string $name, int $stock, int $price_cents, ?string $image_str): ?int
  {
    $stmt = Database::connection()->prepare(<<<SQL
INSERT INTO products (name, stock, price_cents, image, image_md5, supermarket_id) VALUES
    (:name, :stock, :price_cents, :image, :image_md5, :supermarket_id)
SQL
    );
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':stock', $stock);
    $stmt->bindValue(':price_cents', $price_cents);
    $stmt->bindValue(':image', $image_str, $image_str === null ? PDO::PARAM_NULL : PDO::PARAM_LOB);
    $stmt->bindValue(':image_md5', $image_str === null ? null : md5($image_str), $image_str === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':supermarket_id', $supermarket_id);
    $stmt->execute();

    // the id SQLite just generated for this connection's last INSERT.
    // it comes back as a string ("42"), so convert it

    $id = Database::connection()->lastInsertId();

    return !$id ? null : intval($id);
  }

  /**
   * get one product as stocked by one supermarket, with both names
   *
   * @param int $supermarket_id id of the supermarket
   * @param int $product_id id of the product
   *
   * @return array{supermarket_id: int, supermarket_name: string, product_id: int, product_name: string, stock: int, price_cents: int}|null
   *         the row, or null if this supermarket does not stock this product
   *
   * @throws Throwable
   */
  public static function get_by_id(int $supermarket_id, int $product_id): ?array
  {
    $row = Database::query(<<<SQL
SELECT 
  p.supermarket_id AS "supermarket_id",
  s.name AS "supermarket_name",
  p.id AS "product_id",
  p.name AS "product_name",
  p.stock AS "stock",
  p.price_cents AS "price_cents"
FROM products AS p
JOIN supermarkets AS s ON s.id = p.supermarket_id
WHERE p.supermarket_id = :supermarket_id AND p.id = :product_id;
SQL,
      [
        'supermarket_id' => $supermarket_id,
        'product_id'     => $product_id,
      ]
    )->fetch();

    return $row ?: null; // fetch() returns false when there is no row
  }

  /**
   * change the name, stock and price of a product at a supermarket
   *
   * @param int $supermarket_id id of the supermarket
   * @param int $product_id id of the product
   * @param int $stock the new amount in stock (0 or more)
   * @param int $price_cents the new price in cents (0 or more)
   *
   * @return bool true if the row was updated, false if this supermarket does not stock this product
   *
   * @throws Throwable also if stock or price_cents is negative (CHECK constraint in the schema)
   */
  public static function update(int $supermarket_id, int $product_id, string $name, int $stock, int $price_cents): bool
  {
    $stmt = Database::query(<<<SQL
UPDATE products
SET 
  name = :name,
  stock = :stock,
  price_cents = :price_cents
WHERE supermarket_id = :supermarket_id AND id = :product_id
SQL,
      [
        'supermarket_id' => $supermarket_id,
        'product_id'     => $product_id,
        'name'          => $name,
        'stock'          => $stock,
        'price_cents'    => $price_cents,
      ]
    );

    return $stmt->rowCount() > 0; // 0 = no such (supermarket, product) pair
  }

  /**
   * delete an item from a supermarket
   *
   * @param int $product_id id of the product
   *
   * @return bool true if the row was deleted, false if this supermarket does not stock this product
   *
   * @throws Throwable
   */
  public static function delete(int $product_id): bool
  {
    $stmt = Database::query(
      'DELETE FROM products WHERE id = :product_id;',
      [
        'product_id' => $product_id,
      ]
    );

    return $stmt->rowCount() > 0;
  }

  /**
   * get every product stocked by the given supermarket, with its stock and price there
   *
   * @param int $supermarket_id id of the supermarket
   *
   * @return list<array{id: int, name: string, stock: int, price_cents: int}>
   *         one row per product, where name is the product's name and
   *         price_cents is its price at this supermarket;
   *         empty if the supermarket stocks no products
   *
   * @throws Throwable
   */
  public static function get_by_supermarket(int $supermarket_id): array {
    return Database::query(<<<SQL
SELECT id, name, stock, price_cents FROM products
WHERE supermarket_id = :supermarket_id
ORDER BY id DESC
SQL,
      ['supermarket_id' => $supermarket_id],
    )->fetchAll();
  }


  /**
   * get every product stocked by the given supermarket, with its stock and price there
   *
   * @param int $supermarket_id id of the supermarket
   * @param string $search_term search term to filter by name on product
   *
   * @return list<array{id: int, name: string, stock: int, price_cents: int}>
   *         one row per product, where name is the product's name and
   *         price_cents is its price at this supermarket;
   *         empty if the supermarket stocks no products
   *
   * @throws Throwable
   */
  public static function get_by_supermarket_and_name(int $supermarket_id, string $search_term): array {
    $escaped = Database::escape_like($search_term);

    return Database::query(<<<SQL
SELECT id, name, stock, price_cents FROM products
WHERE supermarket_id = :supermarket_id AND name LIKE :pattern ESCAPE '\\' ORDER BY name
SQL,
      ['supermarket_id' => $supermarket_id,
        'pattern'        => '%' . $escaped . '%'],
    )->fetchAll();
  }

  /**
   * get a product image by its id
   *
   * @param int $id the id of the product
   *
   * @return array{image: ?string, image_md5: ?string}|null
   *         image is the raw image bytes and image_md5 its hash (both null if
   *         the product has no image); null if no product has this id
   *
   * @throws Throwable
   */
  public static function get_image(int $id): ?array {
    $product_image = Database::query(
      'SELECT image, image_md5 FROM products WHERE id = :id',
      ['id' => $id]
    )->fetch();

    return $product_image ?: null; // fetch() returns false when there is no row
  }

  /**
   * update product image by id
   *
   * @param int $id the id of the product
   * @param ?string $image the new image bytes, or null to remove the image
   *
   * @return bool true if the product was updated, false if no product has this id
   *
   * @throws Throwable
   */
  public static function update_image(int $id, ?string $image): bool
  {
    $stmt = Database::connection()->prepare(
      'UPDATE products SET image = :image, image_md5 = :image_md5 WHERE id = :id'
    );
    $stmt->bindValue(':id', $id);
    $stmt->bindValue(':image', $image, $image === null ? PDO::PARAM_NULL : PDO::PARAM_LOB);
    $stmt->bindValue(':image_md5', $image === null ? null : md5($image), $image === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->rowCount() > 0;
  }
}
