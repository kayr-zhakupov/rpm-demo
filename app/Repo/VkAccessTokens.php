<?php

namespace App\Repo;

use App\Foundation\Concerns\IsSingleton;
use App\Models\VkAccessTokenRecord;

class VkAccessTokens
{
  use IsSingleton;

  private static VkAccessTokens $instance;

  public function findSession(string $token): ?VkAccessTokenRecord
  {
    if (empty($token)) return null;

    $sql = implode(' ', [
      'SELECT * FROM vk_access_tokens',
      'WHERE `app_token` = :app_token',
      'LIMIT 1',
    ]);

    $statement = app()->db()->statement($sql, [
      'app_token' => $token,
    ]);
    $statement->setFetchMode(\PDO::FETCH_CLASS, VkAccessTokenRecord::class);
    $statement->execute();
    $a = $statement->fetchAll();

    return is_array($a) ? ($a[0] ?? null) : null;
  }

  public function insert(array $values)
  {
    $values = array_intersect_key($values, array_flip([
      'id',
      'app_token',
      'vk_token',
      'user_id',
      'ip_address',
      'expires_at',
      'created_at',
    ]));

    $db = app()->db();

    $sql = $db->sqlInsertQuery('vk_access_tokens', $values);

    $statement = $db->statement($sql, $values);

    $statement->execute();

    return $statement->rowCount();
  }
}