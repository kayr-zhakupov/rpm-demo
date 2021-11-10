<?php

namespace App\Middleware;

use App\Foundation\Concerns\IsSingleton;
use App\Foundation\CookieUtils;

class Auth
{
  use IsSingleton;

  private static Auth $instance;

  public function testMiddleware()
  {
    $sessionToken = CookieUtils::get('app_session');

    $this->testSessionToken($sessionToken);

//    if (Auth::i()->) {
//      app()->router()->runControllerAndDie([new AuthController(), 'index']);
//    }
  }

  private function testSessionToken(string $sessionToken)
  {
    dd(1, $sessionToken);
  }
}