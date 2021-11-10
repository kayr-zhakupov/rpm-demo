<?php

namespace App\Foundation;

class Router
{
  public function runController($controller): AppResponse
  {
    $response = new AppResponse(404);

    if ($controller === null) return $response;

    try {
      $response->setBody($controller());
    } catch (\Throwable $e) {
      error_log($e);
      return $response
        ->setStatus(500)
        ->setBody('500 | Server Error');
    }

    return $response;
  }

  public function runControllerAndDie($controller)
  {
    $response = $this->runController($controller);
    http_response_code($response->getStatus());
    echo $response->renderBody();
    die();
  }
}