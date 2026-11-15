<?= breadcrumbs(['Supermarkets' => '/supermarkets', 'New supermarket' => null]) ?>

<h1>New Supermarket</h1>

<form method="POST" action="/api/supermarkets/create" class="form-stack panel" id="create-supermarket">
  <label for="supermarket_name">Supermarket name</label>
  <input type="text" id="supermarket_name" name="supermarket_name" placeholder="e.g. Pavi Qormi" required />

  <button type="submit">Create Supermarket</button>
</form>
