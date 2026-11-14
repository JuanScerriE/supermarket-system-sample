<?php
/**
 * @var int      $status
 * @var ?string  $message
 */
?>

<section>
  <h1><?= e($status) ?></h1>
  <!-- controllers pass plain text; it's escaped here, exactly once -->
  <p><?= e(!empty($message) ? $message : 'something went wrong.') ?></p>
  <p><a class='button button-secondary' href="/">← Back Home</a></p>
</section>
