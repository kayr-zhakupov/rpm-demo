<?php

namespace App\Controllers;

use App\Foundation\CookieUtils;
use App\Middleware\Auth;
use App\Repo\VkAccessTokens;
use App\Vk\VkAuth;

class AuthController
{
  public function index()
  {
    echo view_html('pages/auth/index');
  }

  public function acceptCode()
  {
    if (Auth::i()->doPassMiddleware()) {
      app()->router()->redirectAndDie(app()->appUrl());
    }

    $code = $_GET['code'] ?? null;

    if (!empty($code)) {
      $this->onReceivingCode($code);
      return;
    }

    if (!empty($_GET)) {
      dd($_GET);
    }

    app()->router()->redirectAndDie(app()->appUrl('authorize'));
  }

  protected function onReceivingCode(string $code)
  {
//    $accessTokenResponse = (new VkAuth())->fetchAccessTokenResponse($code);
    $accessTokenResponse = (new VkAuth())->_mock_fetchAccessTokenResponse_success($code);

    $expiresInSeconds = (int) $accessTokenResponse->getNestedValue('expires_in');

    $appToken = random_alnum(64);

    $db = app()->db();
    $createdAt = new \DateTime();
    $expiresAt = (new \DateTime())->add(new \DateInterval('PT' . $expiresInSeconds . 'S'));

    VkAccessTokens::i()->insert([
      'app_token' => $appToken,
      'vk_token' => $accessTokenResponse->getNestedValue('access_token'),
      'user_id' => $accessTokenResponse->getNestedValue('user_id'),
      'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
      'expires_at' => $db->mysqlDateTimeFormat($createdAt),
      'created_at' => $db->mysqlDateTimeFormat($expiresAt),
    ]);

    CookieUtils::set(
      'app_session',
      $appToken,
      time() + $expiresInSeconds,
    );

    app()->router()->redirectAndDie(app()->appUrl());
  }
}