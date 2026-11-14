<?php
/**
 * @var array{id: int, name: string}|null   $supermarket
 */
?>

<section>
  <h1>Supermarket <?= e($supermarket['name'])?> with ID <?= e($supermarket['id'])?> has been deleted!</h1>
  <p><a class='button button-secondary' href="/">← Back Home</a></p>
</section>
