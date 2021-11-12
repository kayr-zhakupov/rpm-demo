<?php
/**
 * @var ?\Closure $head_cb
 */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Вход</title>
  <?php isset($head_cb) && $head_cb() ?>
</head>