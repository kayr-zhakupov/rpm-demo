<?php

require __DIR__ . '/../boot/app.php';

$sql = "TRUNCATE TABLE vk_access_tokens;";

app()->db()->statement($sql)->execute();

echo "Tables have been cleared" . PHP_EOL;
