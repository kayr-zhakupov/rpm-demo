<?php

use App\Foundation\Application;

function app(): Application
{
  return Application::i();
}

function config(?string $key = null, $default = null)
{
  return Application::i()->config($key, $default);
}

/**
 * Dump-and-die, как в Laravel
 * @param $var
 * @param ...$more
 */
function dd($var, ...$more)
{
  foreach (func_get_args() as $arg) {
    echo "<pre>" . print_r($arg, 1) . "</pre>";
  }
  die();
}

/**
 * Полифилл для str_starts_with (доступный нативно с PHP 8.0), используется в str_replace_prefix()
 */
if (!function_exists('str_starts_with')) {
  function str_starts_with(string $haystack, string $needle): bool
  {
    return 0 === strncmp($haystack, $needle, \strlen($needle));
  }
}

/**
 * Хелпер для автозамены префикса, если он имеется, с одновременным определением - имелся ли требуемый префикс?
 * Используется в автозагрузчике классов.
 * @param string $check_prefix
 * Требуемый префикс
 * @param string $subject
 * @param string $replace
 * @param bool $had_changed
 * Reference для определения наличия префикса у $subject
 * @return string
 */
function str_replace_prefix(
  string $check_prefix, string $subject, string $replace = '', &$had_changed = false
): string
{
  if (str_starts_with($subject, $check_prefix)) {
    $had_changed = true;
    if (($offset = strlen($check_prefix) - strlen($subject)) < 0) {
      return $replace . substr($subject, $offset);
    }
    return $replace;
  }

  return $subject;
}

/**
 * Получение содержимого view в виде html-строки.
 * @param string $path
 * @param array $data
 * @return string
 */
function view_html(string $path, array $data = []): string
{
  ob_start();

  extract($data);

  require APP_BASE_PATH . '/views/' . $path . '.php';

  return ob_get_clean();
}

/**
 * @param $array
 * @param $key
 * @param mixed $default
 * @return mixed
 */
function arr_get($array, $key, $default = null)
{
  if ($key === null) return $array;

  if (!is_array($array)) return $default;

  if (strpos($key, '.') === false) {
    return $array[$key] ?? $default;
  }

  $result = $array;
  foreach (explode('.', $key) as $segment) {
    if (is_array($result) && array_key_exists($segment, $result)) {
      $result = $result[$segment];
      continue;
    }

    return $default;
  }

  return $result;
}

/**
 * Случайная alpha-numeric строка заданной длины (из Laravel).
 *
 * @param int $length
 * @return string
 */
function random_alnum(int $length)
{
  $string = '';

  while (($len = strlen($string)) < $length) {
    $size = $length - $len;

    $bytes = random_bytes($size);

    $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
  }

  return $string;
}
