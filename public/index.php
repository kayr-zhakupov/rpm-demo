<?php

/**
 * Загрузка приложения
 */

use App\Controllers\AjaxController;
use App\Controllers\AuthController;
use App\Controllers\IndexController;

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
    case 'ajax/friends':
      // приём кода авторизации со стороны ВК
      return [new AjaxController(), 'friends'];
    case 'authorize/vk':
      // приём кода авторизации со стороны ВК
      return [new AuthController(), 'acceptCode'];
    case 'authorize':
      // страница авторизации
      return ($method === 'get') ? [new AuthController(), 'index'] : null;
    case '':
      // домашняя страница
      return ($method === 'get') ? new IndexController() : null;
  }

  return null;
});

app()->router()->runControllerAndDie($controller);
