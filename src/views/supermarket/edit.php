<?php
/**
 * @var array{id: int, name: string}   $supermarket
 */
?>

<h1>Edit Supermarket</h1>

<form method="POST" action="/supermarkets/<?= e($supermarket['id']) ?>" class="form-stack" id="edit-supermarket">
  <input type="hidden" name="_method" value="put" />

  <label for="supermarket_name">Supermarket name</label>
  <input type="text" id="supermarket_name" name="supermarket_name"
         value="<?= e($supermarket['name']) ?>" required />

  <button type="submit">Save</button>
</form>

<div class="toolbar">
  <a class='button button-secondary' href="/supermarkets/<?= e($supermarket['id']) ?>">← Back to <?= e($supermarket['name']) ?></a>
  <a class='button button-secondary' href="/">← Home</a>
</div>
