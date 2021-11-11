<?php

require __DIR__ . '/../boot/app.php';

$sql = "DROP TABLE vk_access_tokens;";

app()->db()->statement($sql)->execute();

echo "Database has been wiped" . PHP_EOL;
