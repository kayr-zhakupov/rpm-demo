<?php

require __DIR__ . '/../boot/app.php';

$sql = implode('', [
  "CREATE TABLE vk_access_tokens(",
  implode(', ', [
    "id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
    "app_token VARCHAR(64)",
    "vk_token VARCHAR(64)",
    "ip_address VARCHAR(32)",
    "expires_at TIMESTAMP NOT NULL",
    "created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
  ]),
  ");",
]);

app()->db()->statement($sql)->execute();

echo "Table `vk_access_tokens` has been created" . PHP_EOL;

die();