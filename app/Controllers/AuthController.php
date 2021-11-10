<?php

namespace App\Controllers;

class AuthController
{
  public function index()
  {
    echo view_html('pages/auth/index');
  }

  public function acceptCode()
  {
    $code = $_GET['code'] ?? null;

    if ($code !== null) {
      dd('accept code: ', $code);
    }

    header("Location: " . app()->appUrl('authorize'));
    die();
  }
}