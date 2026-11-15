<?php
/**
 * @var int      $status
 * @var ?string  $message
 */
?>
<section class="panel message">
  <p class="status"><?= e($status) ?></p>
  <!-- controllers pass plain text; it's escaped here, exactly once -->
  <h1><?= e(!empty($message) ? ucfirst($message) : 'Something went wrong.') ?></h1>
  <div class="page-actions">
    <!-- going back keeps what was typed in the form, so it can be fixed and resent -->
    <a class="button button-secondary" href="javascript:history.back()">Go back</a>
    <a class="button" href="/">Home</a>
  </div>
</section>
