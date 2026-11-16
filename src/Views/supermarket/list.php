<?php
/**
 * @var list<array{id: int, name: string}>   $supermarkets
 */

$search_term = (string) ($_GET['search_term'] ?? '');
?>
<div class="page-header">
  <h1>Supermarkets</h1>
  <div class="page-actions">
    <a class="button" href="/supermarkets/create">New Supermarket</a>
  </div>
</div>

<div class="toolbar toolbar-sticky">
  <form method="GET" action="/supermarkets" class="inline-form" id="filter-supermarket" role="search">
    <label for="search_term" class="visually-hidden">Search supermarkets</label>
    <input type="search" placeholder="Search supermarkets…" id="search_term" name="search_term"
           value="<?= e($search_term) ?>">
    <button type="submit">Search</button>
  </form>
</div>

<?php if ($supermarkets === []): ?>
  <p class="empty-state">
    <?= $search_term === '' ? 'No supermarkets yet.' : 'No supermarkets match "' . e($search_term) . '".' ?>
  </p>
<?php else: ?>
  <p class="count"><?= count($supermarkets) ?> supermarket<?= count($supermarkets) === 1 ? '' : 's' ?></p>

  <ul class="cards">
    <?php foreach ($supermarkets as $supermarket): ?>
      <li>
        <a class="card card-tile" href="/supermarkets/<?= e($supermarket['id']) ?>">
          <div class="card-body">
            <h2 class="card-title"><?= e($supermarket['name']) ?></h2>
          </div>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
