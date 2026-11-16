<?php

/**
 * the page frame shared by every view: header with navigation, then the
 * view's own HTML in <main>.
 *
 * @var ?string   $content  the rendered view
 * @var ?string   $title    the page title
 */

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title><?= e($title ?? 'Supermarket Logging System') ?></title>
    <link rel="stylesheet" href="/css/style.css" />
    <!-- <link rel="icon" href="./favicon.ico" type="image/x-icon" /> -->

    <!-- defer: download now, run after the page is parsed, so they can find elements -->
    <script src="/js/index.js" defer></script>
    <script src="/js/image-resize.js" defer></script>
    <script src="/js/camera.js" defer></script>
  </head>
  <body>
    <header class="site-header">
      <div class="site-header-inner">
        <a class="site-name" href="/">Supermarket Logging</a>

        <!-- only visible on phones (see .nav-toggle in style.css) -->
        <button type="button" class="button-secondary nav-toggle"
                aria-controls="site-nav" aria-expanded="false" aria-label="menu">
          <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
        </button>

        <nav class="site-nav" id="site-nav" aria-label="main">
          <?= nav_link('/supermarkets', 'Supermarkets') ?>
        </nav>
      </div>
    </header>

    <main class="container">
      <?= $content ?? '' ?>
    </main>
  </body>
</html>
