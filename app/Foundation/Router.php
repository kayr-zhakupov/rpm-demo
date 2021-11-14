<?php

namespace App\Foundation;

use App\Exceptions\AuthorizationFailedException;
use App\Exceptions\HttpException;
use App\Repo\Routes;

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

      if ($e instanceof AuthorizationFailedException) {
        $this->redirectAndDie(Routes::i()->login());
      } elseif ($e instanceof HttpException) {
        $response
          ->setStatus($status = $e->getStatus())
          ->setBody(sprintf('%d | %s', $status, $e->getMessage()));
      }

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