<?php

/**
 * database connection using PDO (PHP Data Objects).
 *
 * PDO is PHP's built-in, database-agnostic API. the same code works for
 * SQLite, MySQL and PostgreSQL — only the connection string (DSN) changes.
 */

declare(strict_types=1);

namespace App;

use PDO;
use PDOStatement;
use RuntimeException;
use Throwable;

class Database
{
  // `static` = shared by the whole class, not per object. we keep ONE
  // connection per request and reuse it (a simple "singleton").
  private static ?PDO $pdo = null;

  /**
   * @throws Throwable
   */
  public static function connection(): PDO
  {
    if (self::$pdo === null) {
      $db_path = config('db_path');

      // NOTE: needs to happen before the new PDO as that create the file
      $is_new = !file_exists($db_path);

      $dir = dirname($db_path);

      // as per the docs this check whether a file or director exists
      if (!file_exists($dir)) {
        // permission 0775 does the following:
        // 7 -> owner: read, write & execute
        // 7 -> group: read, write & execute
        // 5 -> other: read & execute
        mkdir($dir, 0775, true);
      }

      self::$pdo = new PDO('sqlite:' . $db_path, options: [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

        // fetch() returns ['id' => 1, 'title' => ...] arrays.
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);

      // enable foreign_keys on SQLite
      self::$pdo->exec('PRAGMA foreign_keys = ON');


      if ($is_new) {
        $schema = config('schema_path');

        if (!is_file($schema)) {
          throw new RuntimeException("$schema does not exist");
        }

        // run the whole file in one transaction: all of it applies, or none of it
        self::$pdo->beginTransaction();

        try {
          self::$pdo->exec(file_get_contents($schema));

          // commit the changes
          self::$pdo->commit();
        } catch (Throwable $e) {
          self::$pdo->rollBack();

          // propagate the error
          throw $e;
        }
      }
    }

    return self::$pdo;
  }

  /**
   * @throws Throwable
   */
  public static function query(string $sql, array $params = []): PDOStatement
  {
    $stmt = self::connection()->prepare($sql);
    $stmt->execute($params); // params like this should prevent SQL injection
    return $stmt;
  }

  /**
   * escape LIKE wildcards so user text is matched literally.
   *
   * prepared statements stop SQL injection, but they do NOT touch % and _:
   * inside a LIKE pattern those are still wildcards (% = any text, _ = any
   * single character). this escapes them, plus the escape character itself.
   *
   * the query MUST declare the same escape character:
   *     WHERE name LIKE :pattern ESCAPE '\'
   *
   * @example escape_like('50%_off') // '50\%\_off'
   */
  public static function escape_like(string $value): string
  {
    return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
  }


}

