<?php

namespace App\Foundation;

class Router
{
  public function runController($controller): AppResponse
  {
    $response = new AppResponse(404, '404 | Not Found');

    if ($controller === null) return $response;

    $args = [];
    if (is_array($controller)) {
      $args = array_splice($controller, 2);
    }

    try {

      ob_start();
      $result = $controller(...$args);
      $body = ob_get_clean();

      $response->setBody($result ?? $body);
      $response->setStatus(200);

    } catch (\Throwable $e) {
      error_log($e);
      $response
        ->setStatus(500)
        ->setBody('500 | Server Error');
    }

    $response->sendContentType();

    return $response;
  }

  public function runControllerAndDie($controller)
  {
    $response = $this->runController($controller);
    http_response_code($response->getStatus());
    echo $response->renderBody();
    die();
  }

  public function redirectAndDie(string $destination, int $status = 302)
  {
    http_response_code($status);
    header("Location: " . $destination);
    die();
  }
}