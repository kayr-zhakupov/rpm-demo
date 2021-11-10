<?php

/**
 * Загрузка приложения
 */

use App\Controllers\AuthController;

require __DIR__ . '/../boot/app.php';

/**
 * Роутер
 *
 * Почерк автора: если имеется дело со switch для определения единого значения - оборачиваю его
 * в IIFE, чтобы использовать return и не заботиться о забытых break.
 */
$controller = call_user_func(function () {

  $requestUri = $_SERVER['REQUEST_URI'] ?? '';

  /**
   * Отсечение query-part и лишних слэшей
   */
  $requestUriNoArgs = explode('?', $requestUri)[0] ?? '';
  $requestUriNoArgs = trim($requestUriNoArgs, "/");

  $method = strtolower($_SERVER['REQUEST_METHOD']);

  switch ($requestUriNoArgs) {
    case '':
      // домашняя страница
      return (($method === 'get') ? [new AuthController(), 'index'] : null);
  }

  return null;
});

app()->runControllerAndDie($controller);
