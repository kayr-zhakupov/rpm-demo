<?php

namespace App\Controllers;

use App\Vk\VkAuth;

class AuthController
{
  public function index()
  {
    echo view_html('pages/auth/index');
  }

  public function acceptCode()
  {
    $code = $_GET['code'] ?? null;

    if (!empty($code)) {
      $this->onReceivingCode($code);
      return;
    }

    if (!empty($_GET)) {
      dd($_GET);
    }

    header("Location: " . app()->appUrl('authorize'));
    die();
  }

  protected function onReceivingCode(string $code)
  {
    $accessTokenResponse = (new VkAuth())->fetchAccessToken($code);

    dd($accessTokenResponse);
  }
}