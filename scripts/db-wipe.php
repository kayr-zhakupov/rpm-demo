<?php

require __DIR__ . '/../boot/app.php';

$sql = "DROP TABLE vk_access_tokens;DROP TABLE tags;DROP TABLE tags_with_users;";

app()->db()->statement($sql)->execute();

echo "Database has been wiped" . PHP_EOL;
