<?php

namespace App\Controllers;

use App\Middleware\Auth;

class IndexController
{
  public function __invoke()
  {
    if (Auth::i()->doPassMiddleware()) {
      return $this->friendsIndex();
    };

    app()->router()->runControllerAndDie([new AuthController(), 'index']);
  }

  protected function friendsIndex()
  {
    echo 'friends index';
  }
}