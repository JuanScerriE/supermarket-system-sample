<?php

declare(strict_types=1);

namespace App\Models;

use App\Database;
use PDO;
use Throwable;

class SupermarketProduct {

  /**
   * create a new supermarket + product entry
   *
   * @param int $supermarket_id id of the supermarket
   * @param int $product_id id of the product
   * @param int $stock the amount of product in stock
   * @param int $price_cents the price of the product in cents (e.g. 1 euro => 100 cents)
   *
   * @return void returns nothing
   *
   * @throws Throwable
   *
   * @example create(1, 1, 10, 100)
   */
//  public static function create(int $supermarket_id, int $product_id, int $stock, int $price_cents): void
//  {
//    Database::query(
//      'INSERT INTO supermarket_products (supermarket_id, product_id, stock, price_cents) VALUES (:supermarket_id, :product_id, :stock, :price_cents)',
//      [
//        'supermarket_id' => $supermarket_id,
//        'product_id'     => $product_id,
//        'stock'          => $stock,
//        'price_cents'    => $price_cents,
//      ]
//    );
//  }

  public static function create(int $supermarket_id, string $name, int $stock, int $price_cents, ?string $image_str): ?int
  {
//    Database::query(
//      'INSERT INTO supermarket_products (supermarket_id, product_id, stock, price_cents) VALUES (:supermarket_id, :product_id, :stock, :price_cents)',
//      [
//        'supermarket_id' => $supermarket_id,
//        'product_id'     => $product_id,
//        'stock'          => $stock,
//        'price_cents'    => $price_cents,
//      ]
//    );


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
   *
   * @example get_by_id(1, 10)
   */
  public static function get_by_id(int $supermarket_id, int $product_id): ?array
  {
//    $row = Database::query(<<<SQL
//SELECT supermarket_id, s.name AS "supermarket_name", product_id, p.name AS "product_name", stock, price_cents FROM supermarket_products
//JOIN supermarkets AS s ON s.id = supermarket_id
//JOIN products AS p ON p.id = product_id
//WHERE supermarket_id = :supermarket_id AND product_id = :product_id;
//SQL,
//      [
//        'supermarket_id' => $supermarket_id,
//        'product_id'     => $product_id,
//      ]
//    )->fetch();

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
   * change the stock and price of a product at a supermarket
   *
   * @param int $supermarket_id id of the supermarket
   * @param int $product_id id of the product
   * @param int $stock the new amount in stock (0 or more)
   * @param int $price_cents the new price in cents (0 or more)
   *
   * @return bool true if the row was updated, false if this supermarket does not stock this product
   *
   * @throws Throwable also if stock or price_cents is negative (CHECK constraint in the schema)
   *
   * @example update(1, 10, 25, 199)
   */
  public static function update(int $supermarket_id, int $product_id, string $name, int $stock, int $price_cents): bool
  {
//    $stmt = Database::query(<<<SQL
//UPDATE supermarket_products
//SET stock = :stock, price_cents = :price_cents
//WHERE supermarket_id = :supermarket_id AND product_id = :product_id
//SQL,
//      [
//        'supermarket_id' => $supermarket_id,
//        'product_id'     => $product_id,
//        'stock'          => $stock,
//        'price_cents'    => $price_cents,
//      ]
//    );

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
   * stop a supermarket stocking a product. the supermarket and the product
   * themselves are NOT deleted, only the link between them
   *
   * @param int $supermarket_id id of the supermarket
   * @param int $product_id id of the product
   *
   * @return bool true if the row was deleted, false if this supermarket does not stock this product
   *
   * @throws Throwable
   *
   * @example delete(1, 10)
   */
//  public static function delete(int $supermarket_id, int $product_id): bool
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
   * get every supermarket that stocks the given product, with its stock and price there
   *
   * @param int $product_id id of the product
   *
   * @return list<array{id: int, name: string, stock: int, price_cents: int}>
   *         one row per supermarket, where name is the supermarket's name and
   *         price_cents is the product's price at that supermarket;
   *         empty if no supermarket stocks the product
   *
   * @throws Throwable
   *
   * @example get_by_product(10)
   */
  public static function get_by_product(int $product_id): array {
    // fetchAll() returns [] when there are no rows, never false
    return Database::query(<<<SQL
SELECT supermarket_id AS "id", s.name, stock, price_cents FROM supermarket_products
JOIN supermarkets AS s ON s.id = supermarket_products.supermarket_id
WHERE product_id = :product_id
SQL,
      ['product_id' => $product_id],
    )->fetchAll();
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
   *
   * @example get_by_supermarket(1)
   */
  public static function get_by_supermarket(int $supermarket_id): array {
    // fetchAll() returns [] when there are no rows, never false
//    return Database::query(<<<SQL
//SELECT product_id AS "id", p.name, stock, price_cents FROM supermarket_products
//JOIN products AS p ON p.id = supermarket_products.product_id
//WHERE supermarket_id = :supermarket_id
//ORDER BY product_id DESC
//SQL,
//      ['supermarket_id' => $supermarket_id],
//    )->fetchAll();

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
   *
   * @example get_by_supermarket_and_name(1)
   */
  public static function get_by_supermarket_and_name(int $supermarket_id, string $search_term): array {
    // fetchAll() returns [] when there are no rows, never false

    $escaped = Database::escape_like($search_term);

//    return Database::query(<<<SQL
//SELECT product_id AS "id", p.name, stock, price_cents FROM supermarket_products
//JOIN products AS p ON p.id = supermarket_products.product_id
//WHERE supermarket_id = :supermarket_id AND p.name LIKE :pattern ESCAPE '\\' ORDER BY p.name
//SQL,
//      ['supermarket_id' => $supermarket_id,
//       'pattern'        => '%' . $escaped . '%'],
//    )->fetchAll();

    return Database::query(<<<SQL
SELECT id, name, stock, price_cents FROM products
WHERE supermarket_id = :supermarket_id AND name LIKE :pattern ESCAPE '\\' ORDER BY name
SQL,
      ['supermarket_id' => $supermarket_id,
        'pattern'        => '%' . $escaped . '%'],
    )->fetchAll();
  }
}
