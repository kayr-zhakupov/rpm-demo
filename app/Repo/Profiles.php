<?php

namespace App\Repo;

use App\Foundation\Concerns\IsSingleton;
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
   * общее количество друзей
   ** items: array,
   * ]
   */
  public function fetchFriendsListSlice(?int $count = null, int $offset = 0, array $params = []): array
  {
    $params = $params + [
        'do_include_tags' => true,
      ];

    $slice = VkApi::make()->fetchFriendsList([
      'photo_100', 'online',
    ], [
      'count' => $count,
      'offset' => $offset,
    ]);

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