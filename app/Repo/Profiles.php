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
    return (new VkApi())->fetchMyProfile([
      'photo_400_orig',
    ]);
  }
}