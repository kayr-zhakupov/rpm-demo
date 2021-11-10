<?php

namespace App\Models;

/**
 * Записи извлекаются из БД сразу в виде объектов данного класса.
 * Если бы вдруг понадобились быстрые операции над значениями полей, их можно было бы расположить тут.
 * @see /scripts/db-migrate.php
 *
 * @property int $id
 * @property string $app_token
 * @property string $vk_token
 * @property string $ip_address
 * @property string $expires_at
 * @property string $created_at
 */
class VkAccessTokenRecord
{

}