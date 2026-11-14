<h1>New Supermarket</h1>

<form method="POST" action="/api/supermarkets/create" class="form-stack" id="create-supermarket">
  <label for="supermarket_name">Supermarket name</label>
  <input type="text" id="supermarket_name" name="supermarket_name" placeholder="e.g. Pavi Qormi" required />

  <button type="submit">Create Supermarket</button>
</form>

<div class="toolbar">
  <a class='button button-secondary' href="/supermarkets">← Back</a>
  <a class='button button-secondary' href="/">← Home</a>
</div>
