<?php

namespace App\Foundation;

final class CookieUtils
{
  public static function get(string $name)
  {
    return $_COOKIE[$name] ?? '';
  }

  public static function set(string $name, string $value, int $expires = 0)
  {
    return setcookie(
      $name,
      $value,
      $expires,
      '/',
      app()->host(),
      app()->isHostSecure(),
      true,
    );
  }
}