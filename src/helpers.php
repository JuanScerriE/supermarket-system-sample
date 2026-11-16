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
  require __DIR__ . "/Views/{$template}.php";
  $content = ob_get_clean();

  // ...then print it inside the layout, which echoes $content.
  require __DIR__ . '/Views/layout.php';
}

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

/**
 * a price as a plain number for form inputs: 150 → "1.50" (already escaped).
 * for showing a price to people, use format_price() instead.
 */
function from_cents_to_euro(int $cents): string {
  $price = intval($cents) / 100.0;

  return e(sprintf("%.02f", $price));
}

/** a price for display: 150 → "€1.50" (already escaped) */
function format_price(int $cents): string
{
  return e('€' . number_format($cents / 100, 2));
}

/**
 * render a breadcrumb trail; the last item is the current page.
 *
 * breadcrumbs(['Supermarkets' => '/supermarkets', 'Lidl Valletta' => null])
 *
 * @param array<string, string|null> $items label => link (null = not a link)
 */
function breadcrumbs(array $items): string
{
  $parts = [];
  foreach ($items as $label => $url) {
    // (string): PHP turns a key like "123" into the int 123, so convert it back
    $label = e((string) $label);
    $parts[] = $url === null
      ? "<li aria-current=\"page\">$label</li>"
      : '<li><a href="' . e($url) . "\">$label</a></li>";
  }

  return '<nav class="breadcrumbs" aria-label="breadcrumb"><ol>' . implode('', $parts) . '</ol></nav>';
}

/**
 * a header navigation link, marked as current when the URL is in its section
 * (e.g. /supermarkets/3/products/7 is inside "Supermarkets").
 */
function nav_link(string $url, string $label): string
{
  $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
  $current = $url === '/' ? $path === '/' : str_starts_with($path, $url);

  return '<a href="' . e($url) . '"' . ($current ? ' aria-current="page"' : '') . '>' . e($label) . '</a>';
}


