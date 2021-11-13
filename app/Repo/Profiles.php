<?php

namespace App\Repo;

use App\Foundation\Concerns\IsSingleton;
use App\Models\ProfileData;
use App\Vk\VkApi;

class Profiles
{
  use IsSingleton;

  private static Profiles $instance;

  public function fetchMyProfile(): ProfileData
  {
    return VkApi::make()->fetchMyProfile([
      'photo_200',
    ]);
  }

  /**
   * @return array
   * [
   ** count: int, items: array
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
}