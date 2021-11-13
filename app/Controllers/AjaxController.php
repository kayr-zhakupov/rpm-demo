<?php

namespace App\Controllers;

use App\Repo\Profiles;

class AjaxController
{
  public function friends()
  {
    $count = $_GET['count'] ?? null;
    $offset = $_GET['offset'] ?? 0;

    $slice = Profiles::i()->fetchFriendsListSlice($count, $offset);

    $html = implode('', array_map(function (array $profileData) {
      return view_html('pages/account/friend-tile', [
        'profile' => $profileData,
      ]);
    }, $slice['items']));

    return [
      'html' => $html,
    ];
  }
}