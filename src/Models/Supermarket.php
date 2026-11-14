<?php

declare(strict_types=1);

namespace App\Models;

use App\Database;
use Throwable;


class Supermarket
{
  /**
   * create a new supermarket entry
   *
   * @param string $name name of supermarket
   *
   * @return bool true if the supermarket was created
   *
   * @throws Throwable also if another supermarket already has this name (name is UNIQUE)
   *
   * @example create('Pavi Qormi')
   */
  public static function create(string $name): bool
  {
    $stmt = Database::query(
      'INSERT INTO supermarkets (name) VALUES (:name)',
      [
        'name' => $name,
      ]
    );

    return $stmt->rowCount() > 0;
  }

  // TODO(juan): be careful with this as it can take down the DB
  /**
   * get all supermarkets
   *
   * @return list<array{id: int, name: string}> every supermarket; empty if there are none
   *
   * @throws Throwable
   *
   * @example get_all()
   */
  public static function get_all(): array
  {
    // fetchAll() returns [] when there are no rows, never false
    return Database::query(
      'SELECT id, name FROM supermarkets ORDER BY id DESC',
    )->fetchAll();
  }

  /**
   * get a supermarket by its id
   *
   * @param int $id the id of the supermarket
   *
   * @return array{id: int, name: string}|null the supermarket, or null if no supermarket has this id
   *
   * @throws Throwable
   *
   * @example find_by_id(10)
   */
  public static function find_by_id(int $id): ?array
  {
    // fetch() (not fetchAll()) is correct here: the id is unique, so there is
    // at most one row
    $supermarket = Database::query(
      'SELECT id, name FROM supermarkets WHERE id = :id',
      ['id' => $id]
    )->fetch();

    return $supermarket ?: null; // fetch() returns false when there is no row
  }

  /**
   * rename a supermarket
   *
   * @param int $id the id of the supermarket
   * @param string $name the new name of the supermarket
   *
   * @return bool true if the supermarket was updated, false if no supermarket has this id
   *
   * @throws Throwable also if another supermarket already has this name (name is UNIQUE)
   *
   * @example update_name(10, 'Pavi Qormi')
   */
  public static function update_name(int $id, string $name): bool
  {
    $stmt = Database::query(
      'UPDATE supermarkets SET name = :name WHERE id = :id',
      ['id' => $id, 'name' => $name]
    );

    return $stmt->rowCount() > 0;
  }


  /**
   * search supermarkets whose name contains the given text (case-insensitive)
   *
   * @param string $name text to look for anywhere in the name, e.g. 'pavi' matches 'Pavi Qormi'
   *
   * @return list<array{id: int, name: string}> matching supermarkets sorted by name; empty if none match
   *
   * @throws Throwable
   *
   * @example find_by_name('pavi')
   */
  public static function find_by_name(string $name): array
  {
    // Database::query() only protects against SQL injection; % and _ in $name
    // would still act as LIKE wildcards, so escape them first. without this,
    // searching for "%" returns every supermarket
    $escaped = Database::escape_like($name);

    // in SQLite, LIKE is case-insensitive for ASCII letters (a-z), so
    // 'pavi' matches 'Pavi Qormi'. the wrapping % makes it a "contains" search
    return Database::query(
      "SELECT id, name FROM supermarkets WHERE name LIKE :pattern ESCAPE '\\' ORDER BY name",
      ['pattern' => '%' . $escaped . '%']
    )->fetchAll();
  }

  /**
   * delete a supermarket; its supermarket_products rows are removed too
   * (ON DELETE CASCADE)
   *
   * @param int $id the id of the supermarket
   *
   * @return bool true if a supermarket was deleted, false if no supermarket has this id
   *
   * @throws Throwable
   *
   * @example delete(10)
   */
  public static function delete(int $id): bool
  {
    $stmt = Database::query(
      'DELETE FROM supermarkets WHERE id = :id',
      ['id' => $id]
    );

    return $stmt->rowCount() > 0; // number of rows the DELETE removed
  }
}
