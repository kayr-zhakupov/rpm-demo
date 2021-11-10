<?php

namespace App\Controllers;

class IndexController
{
  public function __invoke()
  {
    if (false) {
      app()->router()->runControllerAndDie([new AuthController(), 'index']);
    }

    $this->friendsList();
  }

  protected function friendsList()
  {
    echo 'hmm';
  }
}