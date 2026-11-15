<?php
/**
 * @var array{id: int, name: string}   $supermarket
 */
?>
<?= breadcrumbs([
  'Supermarkets'       => '/supermarkets',
  $supermarket['name'] => '/supermarkets/' . $supermarket['id'],
  'Rename'             => null,
]) ?>

<h1>Rename Supermarket</h1>

<form method="POST" action="/supermarkets/<?= e($supermarket['id']) ?>" class="form-stack panel" id="edit-supermarket">
  <input type="hidden" name="_method" value="put" />

  <label for="supermarket_name">Supermarket name</label>
  <input type="text" id="supermarket_name" name="supermarket_name"
         value="<?= e($supermarket['name']) ?>" required />

  <button type="submit">Save</button>
</form>
