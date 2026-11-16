<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Supermarket;
use App\Models\Product;
use PDOException;

class SupermarketController
{
  // GET /
  public function home(): void {
    view('home/index', [
      'title'  => 'Home View',
    ]);
  }

  // GET /supermarkets?search_term=lidl
  public function supermarkets(): void {
    // keep the text exactly as typed (spaces, &, ...): the query uses a
    // prepared statement, and the view escapes it when it's displayed
    $search_term = trim((string) ($_GET['search_term'] ?? ''));

    $supermarkets = $search_term === '' ? Supermarket::get_all() : Supermarket::find_by_name($search_term);

    view('supermarket/list', [
      'title'        => 'List of Supermarket(s)',
      'supermarkets' => $supermarkets,
    ]);
  }

  // GET /supermarkets/{id}?search_term=milk
  public function supermarket_by_id(int $id): void {
    $supermarket = Supermarket::find_by_id($id);

    if ($supermarket === null) {
      abort(404, 'supermarket with ID ' . $id . ' does not exist!');
    }

    $search_term = trim((string) ($_GET['search_term'] ?? ''));

    if ($search_term === '') {
      $products = Product::get_by_supermarket($id);
    } else {
      $products = Product::get_by_supermarket_and_name($id, $search_term);
    }

    view('supermarket/single', [
      'title'        => 'View Supermarket',
      'supermarket'  => $supermarket,
      'products'     => $products,
    ]);
  }

  // GET /supermarkets/create
  public function create_view(): void {
    view('supermarket/new', [
      'title' => 'New Supermarket',
    ]);
  }

  // POST /api/supermarkets/create
  public function create(): void {
    $name = trim((string) ($_POST['supermarket_name'] ?? ''));

    if ($name === '') {
      abort(400, 'expected supermarket name');
    }

    try {
      $inserted = Supermarket::create($name);
    } catch (PDOException $e) {
      // 23000 covers every constraint; supermarkets.name is the only UNIQUE one
      if (str_contains($e->getMessage(), 'UNIQUE')) {
        abort(409, 'a supermarket called "' . $name . '" already exists');
      }
      throw $e;
    }

    if (!$inserted) {
      abort(500, 'could not create supermarket!');
    }

    header("Location: /supermarkets", true, 303);   // Post/Redirect/Get

    exit;
  }

  // GET /supermarkets/{id}/edit
  public function update_view(int $id) : void {
    $supermarket = Supermarket::find_by_id($id);

    if ($supermarket === null) {
      abort(404, 'supermarket with ID ' . $id . ' does not exist!');
    }

    view("supermarket/edit", [
      'title'  => 'Edit Supermarket',
      'supermarket' => $supermarket,
    ]);
  }

  // PUT /supermarkets/{id}
  public function update(int $id): void {
    $name = trim((string) ($_POST['supermarket_name'] ?? ''));

    if ($name === '') {
      abort(400, 'expected supermarket name');
    }

    try {
      $updated = Supermarket::update_name($id, $name);
    } catch (PDOException $e) {
      // renaming to a name another supermarket already has
      if (str_contains($e->getMessage(), 'UNIQUE')) {
        abort(409, 'a supermarket called "' . $name . '" already exists');
      }
      throw $e;
    }

    if (!$updated) {
      abort(404, 'supermarket with ID ' . $id . ' does not exist!');
    }

    header("Location: /supermarkets/$id", true, 303);   // Post/Redirect/Get

    exit;
  }

  // DELETE /supermarkets/{id}
  public function delete(int $id): void {
    $supermarket = Supermarket::find_by_id($id);

    if ($supermarket === null) {
      abort(404, 'supermarket with ID ' . $id . ' does not exist!');
    }

    if (!Supermarket::delete($id)) {
      abort(500, 'could not delete supermarket');
    }

    view("supermarket/delete", [
      'title'  => 'Delete Supermarket',
      'supermarket' => $supermarket,
    ]);
  }
}
