<?php

namespace App\Foundation;

final class CookieUtils
{
  public static function get(string $name)
  {
    return $_COOKIE[$name] ?? '';
  }
}