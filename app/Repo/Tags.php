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

  public function insert(array $values, &$lastId = null)
  {
    $values = array_intersect_key($values, array_flip([
      'owner_id',
      'name',
    ]));

    $db = app()->db();
    $sql = $db->sqlInsertQuery('tags', $values);
    ($statement = $db->statement($sql, $values))->execute();
    $lastId = $db->pdo()->lastInsertId();
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

  public function deleteTagToUser($tagId, $targetId)
  {
    $sql = implode(' ', [
      'DELETE tu',
      'FROM tags_with_users tu',
      'INNER JOIN tags t ON tu.tag_id = t.id',
      'WHERE `t`.`owner_id` = :owner_id',
      /**/ 'AND `t`.`id` = :tag_id',
      /**/ 'AND `tu`.`target_id` = :target_id',
    ]);

    $bindings = [
      'owner_id' => Auth::i()->getCurrentUserId(),
      'tag_id' => $tagId,
      'target_id' => $targetId,
    ];

    return app()->db()
      ->executeStatementAndReturn($sql, $bindings)
      ->rowCount();
  }

  public function tagsForProfile($profileId)
  {
    return $this->tagsForProfiles([$profileId]);
  }

  /**
   * @param array $profileIds
   * @return TagRecord[]
   */
  public function tagsForProfiles(array $profileIds): array
  {
    if (empty($profileIds)) return [];

    $inPlaceholders = [];
    $inBindings = [];
    foreach ($profileIds as $i => $profileId) {
      $placeholder = 'p' . $i;
      $inPlaceholders[] = ':' . $placeholder;
      $inBindings[$placeholder] = $profileId;
    }

    $sql = implode(' ', [
      'SELECT',
      db()->compileColumns([
        'id' => 't.id',
        'name' => 't.name',
        'target_id' => 'tu.target_id',
      ]),
      'FROM tags t',
      'INNER JOIN tags_with_users tu ON t.id = tu.tag_id',
      'WHERE `t`.`owner_id` = :owner_id',
      /**/ 'AND `tu`.`target_id` IN (' . implode(',', $inPlaceholders) . ')',
      'ORDER BY `t`.`created_at` ASC',
    ]);

    $statement = app()->db()->statement($sql, [
        'owner_id' => Auth::i()->getCurrentUserId(),
      ] + $inBindings);
    $statement->setFetchMode(\PDO::FETCH_CLASS, TagRecord::class);
    $statement->execute();
    return $statement->fetchAll();
  }
}