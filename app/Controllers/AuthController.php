<?php

namespace App\Controllers;

class AuthController
{
  public function index() {
    echo view_html('pages/auth/index');
  }
}