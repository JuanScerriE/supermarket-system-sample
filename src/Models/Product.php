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
   * @param string      $name   name of product
   * @param string|null $image  raw image bytes (e.g. from file_get_contents()), or null for no image
   *
   * @return ?int returns the id of the newly inserted item
   *
   * @throws Throwable
   *
   * @example create('Coca Cola Can 250ml', file_get_contents('coca-cola.jpg'))
   */
  public static function create(string $name, ?string $image): ?int
  {
    // Database::query() binds everything as a string, which would store the
    // image as TEXT; bind it explicitly as a BLOB (PARAM_LOB) instead.
    // no hex / X'...' needed here: that is only for writing values into SQL text.
    $stmt = Database::connection()->prepare('INSERT INTO products (name, image, image_md5) VALUES (:name, :image, :image_md5)');
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':image', $image, $image === null ? PDO::PARAM_NULL : PDO::PARAM_LOB);
    $stmt->bindValue(':image_md5', $image === null ? null : md5($image), $image === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->execute();

    // the id SQLite just generated for this connection's last INSERT.
    // it comes back as a string ("42"), so convert it

    $id = Database::connection()->lastInsertId();

    return !$id ? null : intval($id);
  }
  // TODO(juan): be careful with this as it can take down the DB
  /**
   * get all products
   *
   * @return list<array{id: int, name: string}> every product; empty if there are none
   *
   * @throws Throwable
   *
   * @example get_all()
   */
  public static function get_all(): array
  {
    // fetchAll() returns [] when there are no rows, never false
    return Database::query(
      'SELECT id, name FROM products ORDER BY id DESC',
    )->fetchAll();
  }

  /**
   * get every product the given supermarket does NOT stock yet (the ones that
   * can still be added to it)
   *
   * @param int $supermarket_id id of the supermarket
   *
   * @return list<array{id: int, name: string}> products sorted by name; empty if it stocks them all
   *
   * @throws Throwable
   *
   * @example get_not_in_supermarket(1)
   */
  public static function get_not_in_supermarket(int $supermarket_id): array
  {
    // NOT EXISTS: keep a product only if no supermarket_products row links it
    // to this supermarket
    return Database::query(<<<SQL
SELECT id, name FROM products AS p
WHERE NOT EXISTS (
  SELECT 1 FROM supermarket_products AS sp
  WHERE sp.product_id = p.id AND sp.supermarket_id = :supermarket_id
)
ORDER BY name
SQL,
      ['supermarket_id' => $supermarket_id],
    )->fetchAll();
  }

  /**
   * get a product by its id
   *
   * @param int $id the id of the product
   *
   * @return array{id: int, name: string}|null the product, or null if no product has this id
   *
   * @throws Throwable
   *
   * @example find_by_id(10)
   */
  public static function find_by_id(int $id): ?array
  {
    // fetch() (not fetchAll()) is correct here: the id is unique, so there is
    // at most one row
    $product = Database::query(
      'SELECT id, name FROM products WHERE id = :id',
      ['id' => $id]
    )->fetch();

    return $product ?: null; // fetch() returns false when there is no row
  }


  /**
   * update product name by id
   *
   * @param int $id the id of the product
   * @param string $name the new name of the product
   *
   * @return bool true if the product was updated, false if no product has this id
   *
   * @throws Throwable
   *
   * @example update_name(10, 'Coca Cola Can 330ml')
   */
  public static function update_name(int $id, string $name): bool
  {
    $stmt = Database::query(
      'UPDATE products SET name = :name WHERE id = :id',
      ['id' => $id, 'name' => $name]
    );

    return $stmt->rowCount() > 0; // 0 = no product has this id
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
   *
   * @example update_image(10, "<image>")
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

    return $stmt->rowCount() > 0; // 0 = no product has this id
  }


  /**
   * search products whose name contains the given text (case-insensitive)
   *
   * @param string $name text to look for anywhere in the name, e.g. 'milk' matches 'Skimmed Milk 1L'
   *
   * @return list<array{id: int, name: string}> matching products sorted by name; empty if none match
   *
   * @throws Throwable
   *
   * @example find_by_name('milk')
   */
  public static function find_by_name(string $name): array
  {
    // Database::query() only protects against SQL injection; % and _ in $name
    // would still act as LIKE wildcards, so escape them first. without this,
    // searching for "%" returns every product
    $escaped = Database::escape_like($name);

    // in SQLite, LIKE is case-insensitive for ASCII letters (a-z), so
    // 'milk' matches 'Skimmed Milk 1L'. the wrapping % makes it a "contains" search
    return Database::query(
      "SELECT id, name FROM products WHERE name LIKE :pattern ESCAPE '\\' ORDER BY name",
      ['pattern' => '%' . $escaped . '%']
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
   *
   * @example get_image(10)
   */
  public static function get_image(int $id): ?array {
    $product_image = Database::query(
      'SELECT image, image_md5 FROM products WHERE id = :id',
      ['id' => $id]
    )->fetch();

    return $product_image ?: null; // fetch() returns false when there is no row
  }

  /**
   * delete a product; its supermarket_products rows are removed too
   * (ON DELETE CASCADE)
   *
   * @param int $id the id of the product
   *
   * @return bool true if a product was deleted, false if no product has this id
   *
   * @throws Throwable
   *
   * @example delete(10)
   */
  public static function delete(int $id): bool
  {
    $stmt = Database::query(
      'DELETE FROM products WHERE id = :id',
      ['id' => $id]
    );

    return $stmt->rowCount() > 0; // number of rows the DELETE removed
  }
}
