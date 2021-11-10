<?php

namespace App\Middleware;

use App\Foundation\Concerns\IsSingleton;
use App\Foundation\CookieUtils;
use App\Models\VkAccessTokenRecord;

class Auth
{
  use IsSingleton;

  private static Auth $instance;

  /**
   * @return bool
   * true, если посетитель авторизован и токен активен; false otherwise
   */
  public function doPassMiddleware(): bool
  {
    $sessionToken = CookieUtils::get('app_session');

    $this->testSessionToken($sessionToken);

    return false;

//    if (Auth::i()->) {
//      app()->router()->runControllerAndDie([new AuthController(), 'index']);
//    }
  }

  private function testSessionToken(string $sessionToken): bool
  {
    if (empty($sessionToken)) return false;

    $vkTokenRecord = $this->retrieveSessionRecord($sessionToken);

    if (empty($vkTokenRecord)) return false;

    dd(__METHOD__, 1, $sessionToken);
  }

  protected function retrieveSessionRecord(string $sessionToken): ?VkAccessTokenRecord
  {
    return null;
  }
}