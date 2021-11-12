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
  <link href="<?= app()->styleUrl('gen/base.css?v=0.0.1') ?>" rel="stylesheet"/>
  <?php isset($head_cb) && $head_cb() ?>
</head>