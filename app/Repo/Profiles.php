<?php

namespace App\Repo;

use App\Foundation\Concerns\IsSingleton;
use App\Middleware\Auth;
use App\Models\ProfileData;
use App\Vk\VkApi;

class Profiles
{
  use IsSingleton;

  private static Profiles $instance;

  public function vkApi()
  {
    return VkApi::make();
  }

  public function fetchMyProfile(): ProfileData
  {
    return $this->fetchProfileById(null);
  }

  /**
   * @return array
   * [
   ** count: int,
   * общее количество позиций
   ** items: array,
   * ]
   */
  public function fetchFriendsOrTaggedProfilesListSlice(?int $count = null, int $offset = 0, array $params = []): array
  {
    $params = $params + [
        'do_include_tags' => true,
        'tags' => '',
      ];

    $tags = (function ($in) {
      if (empty($in)) return [];

      if (is_string($in)) {
        $in = explode(',', $in);
      }

      return array_filter($in);
    })($params['tags']);

    if ($tags) {
      $items = $this->fetchProfilesByTags($tags, $count, $offset, $params);
      $slice = [
        'count' => count($items),
        'items' => $items,
      ];
    } else {
      $slice = $this->fetchFriendsListSlice($count, $offset);
    }

    if ($params['do_include_tags']) {
      $slice['items'] = $this->extendSliceItemsWithTags($slice['items']);
    }

    return $slice;
  }

  /**
   * @return array
   * [
   ** count: int,
   * общее количество друзей
   ** items: array,
   * ]
   */
  public function fetchFriendsListSlice(?int $count = null, int $offset = 0): array
  {
    return VkApi::make()->fetchFriendsList([
      'photo_100', 'online',
    ], [
      'count' => $count,
      'offset' => $offset,
    ]);
  }

  /**
   * @param array $tags
   * @param int|null $count
   * @param int $offset
   * @param array $params
   * @return array[]
   * @throws \Exception
   */
  public function fetchProfilesByTags(array $tags, ?int $count = null, int $offset = 0, array $params = [])
  {
    $inPlaceholders = [];
    $inBindings = [];
    $tags = array_unique($tags);
    foreach ($tags as $i => $profileId) {
      $placeholder = 'p' . $i;
      $inPlaceholders[] = ':' . $placeholder;
      $inBindings[$placeholder] = $profileId;
    }

    $sql = implode(' ', [

      'SELECT `tu`.`target_id` AS target_id',
      'FROM tags_with_users tu',
      'INNER JOIN tags t ON tu.tag_id = t.id',
      'WHERE `t`.`owner_id` = :owner_id',
      /**/ 'AND `tu`.`tag_id` IN (' . implode(',', $inPlaceholders) . ')',
      'GROUP BY `tu`.`target_id`',
      'HAVING COUNT(DISTINCT `tu`.`tag_id`) >= :tags_count',
      'ORDER BY MAX(`tu`.`id`) DESC',
      'LIMIT ' . $count,
      'OFFSET ' . $offset,
    ]);

    $statement = app()->db()->statement($sql, [
        'owner_id' => Auth::i()->getCurrentUserId(),
        'tags_count' => count($tags),
      ] + $inBindings);
    $statement->execute();
    $profileIds = arr_pluck($statement->fetchAll(), 'target_id');

    return $profileIds
      ? VkApi::make()->fetchProfiles($profileIds, [
        'photo_100', 'online',
      ])
      : [];
  }

  /**
   * @return array
   * [
   ** count: int,
   * общее количество друзей
   ** items: array,
   * ]
   */
  public function fetchMutualFriendsListSlice($sourceId, $targetId, ?int $count = null, int $offset = 0)
  {
    $ids = $this->vkApi()->fetchMutualFriendsIds($sourceId, $targetId);

    $slicedIds = array_splice($ids, $offset, $count);

    $profiles = $this->vkApi()->fetchProfiles($slicedIds, [
      'photo_100', 'online',
    ]);

    return [
      'count' => count($ids),
      'items' => $profiles,
    ];
  }

  /**
   * @param int|null $id
   * @return \App\Models\ProfileData
   */
  public function fetchProfileById($id)
  {
    return VkApi::make()->fetchSingleProfile($id, [
      'photo_200',
    ]);
  }

  protected function extendSliceItemsWithTags($profileItems)
  {
    $profilesGroupedById = arr_key_by($profileItems, 'id');
    $tagsForSlice = Tags::i()->tagsForProfiles(array_keys($profilesGroupedById));

    foreach ($tagsForSlice as $tagRecord) {
      $targetId = $tagRecord->target_id;

      if (!key_exists('tags', $profilesGroupedById[$targetId])) {
        $profilesGroupedById[$targetId]['tags'] = [];
      }

      $profilesGroupedById[$targetId]['tags'][] = $tagRecord;
    }

    return array_values($profilesGroupedById);
  }
}