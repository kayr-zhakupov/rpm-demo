<?php

namespace App\Repo;

use App\Foundation\Concerns\IsSingleton;

class VkAccessTokens
{
  use IsSingleton;

  private static VkAccessTokens $instance;

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
    dd($values);

    $db = app()->db();

    $sql = $db->sqlInsertQuery('vk_access_tokens', $values);

    return $db->statement($sql, $values)->rowCount();
  }
}