<?php
/**
 * @var array{id: int, name: string}|null   $product
 */
?>

<section>
  <h1>Product <?= e($product['name'])?> with ID <?= e($product['id'])?> has been deleted!</h1>
  <p><a class='button button-secondary' href="/">← Back Home</a></p>
</section>
