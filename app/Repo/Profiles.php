<?php

namespace App\Repo;

use App\Foundation\Concerns\IsSingleton;
use App\Middleware\Auth;
use App\Profile\ProfilesSliceRequest;
use App\Vk\VkApi;

class Profiles
{
  use IsSingleton;

  private static Profiles $instance;

  public function vkApi()
  {
    return VkApi::make();
  }

  /**
   * @return array
   * [
   ** count: int,
   * общее количество друзей
   ** items: array,
   * ]
   */
  public function fetchFriendsListSlice(ProfilesSliceRequest $request): array
  {
    return VkApi::make()->fetchFriendsList([
      'photo_100', 'online',
    ], [
      'count' => $request->getRequestedCount(),
      'offset' => $request->getOffset(),
    ]);
  }

  /**
   * @param ProfilesSliceRequest $request
   * @return array
   * [
   ** count: int,
   * общее количество
   ** items: array,
   * ]
   * @throws \Exception
   */
  public function fetchProfilesByTags(ProfilesSliceRequest $request)
  {
    $inPlaceholders = [];
    $inBindings = [];
    $tags = $request->getTags();
    foreach ($tags as $i => $profileId) {
      $placeholder = 'p' . $i;
      $inPlaceholders[] = ':' . $placeholder;
      $inBindings[$placeholder] = $profileId;
    }

    $allBindings = [
        'owner_id' => Auth::i()->getCurrentUserId(),
        'tags_count' => count($tags),
      ] + $inBindings;

    $sqlCore = implode(' ', [
      'FROM tags_with_users tu',
      'INNER JOIN tags t ON tu.tag_id = t.id',
      'WHERE `t`.`owner_id` = :owner_id',
      /**/ 'AND `tu`.`tag_id` IN (' . implode(',', $inPlaceholders) . ')',
      'GROUP BY `tu`.`target_id`',
      'HAVING COUNT(DISTINCT `tu`.`tag_id`) >= :tags_count',
    ]);

    $sqlCount = implode(' ', [
      'SELECT COUNT(*) AS total FROM (',
      /**/ 'SELECT `tu`.`target_id`',
      /**/ $sqlCore,
      ') total',
    ]);

    $countStatement = app()->db()->statement($sqlCount, $allBindings);
    $countStatement->execute();
    $totalCount = arr_get($countStatement->fetchAll(), '0.total');

    $sql = implode(' ', [
      'SELECT `tu`.`target_id` AS target_id',
      $sqlCore,
      'ORDER BY MAX(`tu`.`id`) DESC',
      'LIMIT ' . $request->getRequestedCount(),
      'OFFSET ' . $request->getOffset(),
    ]);

    $statement = app()->db()->statement($sql, [
        'owner_id' => Auth::i()->getCurrentUserId(),
        'tags_count' => count($tags),
      ] + $inBindings);
    $statement->execute();
    $profileIds = arr_pluck($statement->fetchAll(), 'target_id');

    return [
      'items' => $profileIds
        ? VkApi::make()->fetchProfiles($profileIds, [
          'photo_100', 'online',
        ])
        : [],
      'count' => $totalCount,
    ];
  }

  /**
   * @return array
   * [
   ** count: int,
   * общее количество друзей
   ** items: array,
   * ]
   */
  public function fetchMutualFriendsListSlice(ProfilesSliceRequest $request)
  {
    ['count' => $count, 'ids' => $ids] = $this->vkApi()
      ->fetchMutualFriendsIds(
        $request->getFriendsOfId(), $request->getMutualFriendsWith(), [
          'count' => $request->getRequestedCount(),
          'offset' => $request->getOffset(),
        ]
      );

    $profiles = $this->vkApi()->fetchProfiles($ids, [
      'photo_100', 'online',
    ]);

    return [
      'count' => $count,
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

  public function extendSliceItemsWithTags($profileItems)
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

  /**
   * Если хотя бы один пользователь удалён, выдаётся общая ошибка 18.
   * @param $profileItems
   * @return array|void[]
   */
  public function extendSliceItemsWithMutualFriendsCount($profileItems)
  {
    $allProfilesGroupedById = arr_key_by($profileItems, 'id');
    $profilesFiltered = array_filter(
      $allProfilesGroupedById,
      function ($profile) {
        return empty(arr_get($profile, 'deactivated'));
      }
    );

    if (!empty($profilesFiltered)) {
      $stats = $this->vkApi()
        ->fetchMutualFriendsMultiple(
          Auth::i()->getCurrentUserId(),
          array_keys($profilesFiltered),
        );

      foreach ($stats as $stat) {
        $profileId = $stat['id'];

        $allProfilesGroupedById[$profileId]['common_count'] = $stat['common_count'];
      }
    }

    return array_values($allProfilesGroupedById);
  }
}