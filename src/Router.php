<?php
/**
 * a minimal router: maps (HTTP method + URL path) to a controller method.
 */

declare(strict_types=1);

namespace App;

use Uri\WhatWg;

class Router
{
  private array $routes = [];

  public function get(string $path, array $handler): void    { $this->add('GET', $path, $handler); }
  public function post(string $path, array $handler): void   { $this->add('POST', $path, $handler); }
  public function put(string $path, array $handler): void    { $this->add('PUT', $path, $handler); }
  public function delete(string $path, array $handler): void { $this->add('DELETE', $path, $handler); }

  private function add(string $method, string $path, array $handler): void
  {
    $this->routes[$method][$path] = $handler;
  }

  public function dispatch(string $method, string $uri): void
  {
    $parsed_uri = WhatWg\Url::parse("https://router.invalid$uri");

    if ($parsed_uri === null) {
      // TODO(juan): check that the error makes sense
      abort(404, 'malformed URL.');
    }

    $path = $parsed_uri->getPath();

    // method spoofing: a form with <input name="_method" value="DELETE">
    // is treated as a DELETE request.
    if ($method === 'POST' && isset($_POST['_method'])) {
      $spoofed = strtoupper((string)$_POST['_method']);
      if (in_array($spoofed, ["PUT", "DELETE"], true)) {
        $method = $spoofed;
      }
    }

    $convert_to_int = function (string $str) {
      return intval($str);
    };

    foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
      $regex = '#^' . preg_replace('#\{\w+}#', '(\d+)', $pattern) . '$#';

      if (preg_match($regex, $path, $matches)) {
        array_shift($matches);                 // drop the full match
        $params = array_map($convert_to_int, $matches);

        [$class, $action] = $handler;           // array destructuring
        $controller = new $class();             // class name stored in a string
        $controller->$action(...$params);       // spread params as arguments
        return;
      }
    }

    // No route matched.
    abort(404, 'page not found.');
  }
}
