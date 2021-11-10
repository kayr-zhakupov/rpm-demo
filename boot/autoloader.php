<?php

spl_autoload_register(function($class) {

  $classTr = strtr($class, '\\', '/');

  $relPath = str_replace_prefix('App/', $classTr, 'app/', $hadPrefix);
  if ($hadPrefix) {
    require APP_BASE_PATH . '/' . $relPath . '.php';
  }
});