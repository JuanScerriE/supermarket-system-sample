<?php

/**
 * render a view inside the shared layout.
 */
function view(string $template, array $data = []): void
{
  // extract() turns ['notes' => ...] into a local variable $notes.
  // TODO(juan): figure out if there is a better way of doing this cause this is a bit dangerous
  extract($data);

  // output buffering: capture the template's output into a string
  // instead of sending it straight to the browser...
  ob_start();
  require __DIR__ . "/views/{$template}.php";
  $content = ob_get_clean();

  // ...then print it inside the layout, which echoes $content.
  require __DIR__ . '/views/layout.php';
}

/**
 * escape a value for safe output in HTML.
 *
 * THE most important security habit in PHP: always escape user data before
 * echoing it. Otherwise, someone could save a note containing
 * <script>...</script> and it would run in other people's browsers (XSS).
 */
function e(mixed $value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** show an error page and stop. */
function abort(int $status, string $message = ''): never
{
  http_response_code($status);
  view('errors/error', [
    'title' => "Error $status",
    'status' => $status,
    'message' => $message
  ]);
  exit;
}

/** read a config value: config('app_name') */
function config(string $key, mixed $default = null): mixed
{
  return $GLOBALS['config'][$key] ?? $default;
}

function from_cents_to_euro(int $cents): string {
  $price = intval($cents) / 100.0;

  return e(sprintf("%.02f", $price));
}


