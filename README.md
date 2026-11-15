# Supermarket System

A small supermarket stock and price manager written in plain PHP 8.5. It has no framework and stores everything in a single SQLite file. It was built as a learning project, so the code is heavily commented and shows how a front controller, a router, controllers, models and views fit together.

## Features

- **Supermarkets**: create, list, search, rename and delete supermarkets.
- **Products**: create, list, search, update and delete products. Each product can have an optional image.
- **Stock and prices per supermarket**: link a product to a supermarket with its own stock level and price, then update or remove that link. Prices are stored in cents to avoid floating-point rounding errors.
- **Search**: case-insensitive "contains" search on supermarkets, products, and the products inside a single supermarket. The search is kept when you return to a list after editing.
- **Product images**
  - Accepts JPEG, PNG and WebP. The file type is checked from the file's bytes, not from its extension.
  - Images are resized in the browser before upload, then resized again on the server with GD to a maximum of 512px.
  - Images are stored in the database and served with an `ETag`, so browsers don't download an unchanged image again.
  - Products without an image show a placeholder.
- **Camera capture**: take a product photo directly with the device's camera (selfie or webcam). This needs HTTPS or `localhost`. If the camera isn't available, the button is hidden and normal file upload still works.
- **Proper HTTP handling**
  - Forms follow Post/Redirect/Get with `303` redirects.
  - `PUT` and `DELETE` are sent from forms through a hidden `_method` field.
  - Errors return the right status code, such as `400`, `404`, `413` for an upload that is too large, and `415` for an unsupported image type.
- **Safe by default**: every query uses a prepared statement, and all output is escaped. Only `public/` is exposed to the web server.
- **Database tooling**
  - `bin/db` creates, seeds, resets, drops or opens the SQLite database.
  - `bin/generate-seed` generates a large, reproducible set of fake data.

## Project layout

```
public/          web root (index.php front controller, css, js, assets)
src/             Router, Database, Controllers, Models, views, helpers
schema/          create_db.sql, seed.sql and seed images
bin/db           database CLI
bin/generate-seed  fake data generator
config.php       paths to the database and schema
database/        app.sqlite (created on first run, not committed)
```

## Setup

There are two ways to run the app. Docker is the easiest because PHP and its extensions come with the image.

### Option 1: Docker

Requirements: [Docker](https://docs.docker.com/get-docker/) with Docker Compose.

```sh
git clone <repo-url> supermarket-system
cd supermarket-system
docker compose up --build
```

Open <http://localhost:5173>.

- On the first start, the container creates the database and seeds it with test data (see `docker/entrypoint.sh`).
- The database is stored in the named volume `db-data`, so your data survives `docker compose down` and rebuilds.
- To start again with a fresh database, run:

  ```sh
  docker compose down -v      # -v also deletes the db-data volume
  docker compose up --build
  ```

- To run database commands inside the running container:

  ```sh
  docker compose exec app php bin/db reset
  ```

The image is based on Alpine Linux and installs PHP 8.5 with the `gd`, `pdo_sqlite` and `fileinfo` extensions. Uploads are limited to 8 MB per file and 10 MB per request.

### Option 2: PHP and Composer directly

Requirements:

- **PHP 8.5 or newer.** The app uses the built-in `Uri` extension, which first ships with 8.5.
- **[Composer](https://getcomposer.org/)**
- The PHP extensions **`gd`**, **`fileinfo`** and **`pdo_sqlite`**
- (Optional) the `sqlite3` CLI, which is only needed for `bin/db shell`.

#### 1. Enable the required extensions

These three extensions are often disabled by default, so you need to turn them on in `php.ini`.

1. Find the `php.ini` file your PHP is using:

   ```sh
   php --ini
   ```

   Look at the line `Loaded Configuration File`.

2. Open that file and find these lines. Remove the leading `;` from each one to enable it:

   ```ini
   extension=fileinfo
   extension=gd
   extension=pdo_sqlite
   ```

   On Windows, also make sure `extension_dir` points to your PHP `ext` folder, for example `extension_dir = "ext"`.

3. Images are uploaded through POST, so it's worth raising PHP's default 2 MB upload limit in the same file:

   ```ini
   upload_max_filesize = 8M
   post_max_size = 10M
   ```

4. Check that the extensions are loaded:

   ```sh
   php -m | grep -iE 'gd|fileinfo|pdo_sqlite'
   ```

   All three should be listed.

> **Homebrew (macOS) note:** Homebrew's PHP already has `gd`, `fileinfo` and `pdo_sqlite` built in. If `php -m` already lists them, leave those `extension=` lines commented out. Enabling them again causes "Unable to load dynamic library" warnings.

#### 2. Install and set up the database

```sh
git clone <repo-url> supermarket-system
cd supermarket-system
composer install          # generates the autoloader in vendor/
composer db:create        # creates database/app.sqlite with the tables
composer db:seed          # (optional) loads test data from schema/seed.sql
```

#### 3. Run the app

```sh
composer serve
```

Open <http://localhost:8000>.

| Script | What it does |
| --- | --- |
| `composer serve` | Dev server on `localhost:8000` with all errors shown |
| `composer serve:debug` | Same as `serve`, but starts an Xdebug session on every request |
| `composer serve:lan` | Serves on `0.0.0.0:5173` with 4 workers and errors hidden, so other devices on your network can reach it |
| `composer db:create` | Create the database file and tables (safe to re-run) |
| `composer db:seed` | Insert the test data |
| `composer db:reset` | Delete the database, then create and seed it again |
| `composer db:generate-seed` | Regenerate `schema/seed.sql` with fake data |

> The camera only works over HTTPS or on `localhost`. With `serve:lan`, other devices connect over plain HTTP, so they get the file picker instead of the camera button.

## Database CLI

```sh
php bin/db create   # create the database file and tables (safe to re-run)
php bin/db seed     # insert test data from schema/seed.sql
php bin/db reset    # delete the database, then create and seed it from scratch
php bin/db drop     # delete the database file
php bin/db shell    # open the database in the sqlite3 shell
```

Generate a different data set, then load it:

```sh
php bin/generate-seed --supermarkets=50 --products=5000 --coverage=0.3 --seed=42
php bin/db reset
```

Run `php bin/generate-seed --help` to see all the options. If a file named `schema/images/<type>.jpg` exists (for example `coffee.jpg`), every product of that type uses it as its image. Other product types get a generated placeholder.
