<?php

require __DIR__ . '/../boot/app.php';

$createTableCb = function (string $table, array $columns) {
  try {
    $sql = 'CREATE TABLE ' . $table . '(' . implode(', ', array_filter($columns)) . ');';

    app()->db()->statement($sql, ['table' => $table])->execute();
    echo sprintf('Table `%s` has been created', $table) . PHP_EOL;
  } catch (\Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
  }
};

$createTableCb(
  'vk_access_tokens', [
    "id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
    "app_token VARCHAR(64) NOT NULL UNIQUE",
    "vk_token VARCHAR(128) NOT NULL",
    "user_id VARCHAR(16) NOT NULL",
    "ip_address VARCHAR(32) NOT NULL",
    "expires_at TIMESTAMP NOT NULL",
    "created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
  ]
);

$createTableCb(
  'tags', [
    "id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
    "owner_id VARCHAR(16) NOT NULL",
    "name VARCHAR(64) NOT NULL",
    "created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
  ]
);

$createTableCb(
  'tags_with_users', [
    "id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY",
    "target_id VARCHAR(16) NOT NULL",
    "tag_id BIGINT UNSIGNED NOT NULL",
    "is_deleted BOOL NOT NULL DEFAULT 0",
    "created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
  ]
);

die();