<?php

namespace App\Repo;

use App\Foundation\Concerns\IsSingleton;
use App\Middleware\Auth;
use App\Models\TagRecord;

class Tags
{
  use IsSingleton;

  private static Tags $instance;

  /** @var \App\Models\TagRecord[] */
  protected array $allMyTagsCached;

  public function getAllMyTags()
  {
    if (!isset($this->allMyTagsCached)) {
      $sql = implode(' ', [
        'SELECT * FROM tags',
        'WHERE `owner_id` = :owner_id',
      ]);

      $statement = app()->db()->statement($sql, [
        'owner_id' => Auth::i()->getCurrentUserId(),
      ]);
      $statement->setFetchMode(\PDO::FETCH_CLASS, TagRecord::class);
      $statement->execute();
      $this->allMyTagsCached = $statement->fetchAll();
    }

    return $this->allMyTagsCached;
  }

  public function insert(array $values)
  {
    $values = array_intersect_key($values, array_flip([
      'owner_id',
      'name',
    ]));

    $db = app()->db();
    $sql = $db->sqlInsertQuery('tags', $values);
    ($statement = $db->statement($sql, $values))->execute();
    return $statement->rowCount();
  }

  public function insertTagToUser(array $values)
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

  public function tagsForProfile($profileId)
  {
    $sql = implode(' ', [
      'SELECT * FROM tags t ',
      'JOIN tags_with_users tu ON t.id = tu.tag_id',
      'WHERE `owner_id` = :owner_id',
      /**/'AND `target_id` = :target_id',
    ]);

    $statement = app()->db()->statement($sql, [
      'owner_id' => Auth::i()->getCurrentUserId(),
      'target_id' => $profileId,
    ]);
    $statement->setFetchMode(\PDO::FETCH_CLASS, TagRecord::class);
    $statement->execute();
    return dd($statement->fetchAll());
  }
}