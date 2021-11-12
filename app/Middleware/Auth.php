<?php

namespace App\Middleware;

use App\Foundation\Concerns\IsSingleton;
use App\Foundation\CookieUtils;
use App\Models\VkAccessTokenRecord;
use App\Repo\VkAccessTokens;

class Auth
{
  use IsSingleton;

  private static Auth $instance;

  private ?VkAccessTokenRecord $currentSession;

  /**
   * @return bool
   * true, если посетитель авторизован и токен активен; false otherwise
   */
  public function doPassMiddleware(): bool
  {
    $sessionToken = CookieUtils::get('app_session');

    return $this->authorizeAppToken($sessionToken);
  }

  protected function authorizeAppToken($sessionToken)
  {
    if (!isset($this->currentSession) || ($this->currentSession->app_token !== $sessionToken)) {
      $this->currentSession = $this->retrieveSessionRecord($sessionToken);
    }

    return ($this->currentSession !== null);
  }

  protected function retrieveSessionRecord(string $sessionToken): ?VkAccessTokenRecord
  {
    return VkAccessTokens::i()->findSession($sessionToken);
  }

  public function getCurrentVkAccessToken(): ?string
  {
    if (!isset($this->currentSession) || ($this->currentSession === null)) return null;

    return $this->currentSession->vk_token;
  }
}