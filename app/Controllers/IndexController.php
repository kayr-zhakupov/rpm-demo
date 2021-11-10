<?php

namespace App\Controllers;

use App\Middleware\Auth;

class IndexController
{
  public function __invoke()
  {
    Auth::i()->testMiddleware();

    $this->friendsList();
  }

  protected function friendsList()
  {
    echo 'hmm';
  }
}