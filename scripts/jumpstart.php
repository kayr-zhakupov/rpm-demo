<?php

define('APP_BASE_PATH', realpath(__DIR__ . '/..'));

if (!is_dir($d = APP_BASE_PATH . '/logs')) {
  mkdir($d);
  echo 'Папка `logs` создана' . PHP_EOL;
}

if (!is_file($f = APP_BASE_PATH . '/env/env.php')) {
  mkdir(APP_BASE_PATH . '/env');
  copy(APP_BASE_PATH . '/env.example.php', $f);
  echo 'Файл параметров окружения `env/env.php` создан' . PHP_EOL;
}

if (!is_file($f = APP_BASE_PATH . '/public/favicon.ico')) {
  touch($f);
  echo 'Пустой favicon.ico создан' . PHP_EOL;
}

echo '===' . PHP_EOL;
echo '*** Success ***' . PHP_EOL;
