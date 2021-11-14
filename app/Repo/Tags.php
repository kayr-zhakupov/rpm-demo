<?php

namespace App\Repo;

use App\Foundation\Concerns\IsSingleton;

class Tags
{
  use IsSingleton;

  private static Tags $instance;

  public function insert(array $values)
  {
    $values = array_intersect_key($values, array_flip([
      'owner_id',
      'target_id',
      'tag_id',
    ]));

    $db = app()->db();
    $sql = $db->sqlInsertQuery('tags_with_users', $values);
    ($statement = $db->statement($sql, $values))->execute();
    return $statement->rowCount();
  }
}