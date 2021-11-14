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

  public function retrievePersistedSessionToken()
  {
    return CookieUtils::get('app_session');
  }

  public function ensureCurrentSession(): ?VkAccessTokenRecord
  {
    if (!isset($this->currentSession)) {
      $this->currentSession = $this->retrieveSessionRecord($this->retrievePersistedSessionToken());
    }

    return $this->currentSession;
  }

  /**
   * @return bool
   * true, если посетитель авторизован и токен активен; false otherwise
   */
  public function doPassMiddleware(): bool
  {
    $this->ensureCurrentSession();
    return ($this->currentSession !== null);
  }

  protected function retrieveSessionRecord(string $sessionToken): ?VkAccessTokenRecord
  {
    return VkAccessTokens::i()->findSession($sessionToken);
  }

  public function getCurrentVkAccessToken(): ?string
  {
    $this->ensureCurrentSession();

    if ($this->currentSession === null) return null;

    return $this->currentSession->vk_token;
  }

  public function getCurrentUserId(): ?string
  {
    $this->ensureCurrentSession();

    if ($this->currentSession === null) return null;

    return $this->currentSession->user_id;
  }
}