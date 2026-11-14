<?php

/**
 * Application configuration.
 *
 * a PHP file can `return` a value. When another file does
 * `$config = require 'config.php';` it receives this array.
 * this is a common, dependency-free way to store settings.
 */

return [
  'base_path' => __DIR__,

  // SQLite stores the whole database in a single file. It is created
  // automatically on first run.
  'db_path' => __DIR__ . '/database/app.sqlite',
  'schema_path' => __DIR__ . '/schema/create_db.sql',
];
