<?php

namespace App\Controllers;

use App\Middleware\Auth;
use App\Repo\Profiles;

class IndexController
{
  public function __invoke()
  {
    if (Auth::i()->doPassMiddleware()) {
      $this->accountIndex();
      return;
    };

    app()->router()->runControllerAndDie([new AuthController(), 'index']);
  }

  protected function accountIndex()
  {
    $profile = Profiles::i()->fetchMyProfile();
    $sliceCountInitial = config('friends_slice_count_initial');
    $friendsSlice = Profiles::i()->fetchFriendsListSlice($sliceCountInitial);
    $friendsSliceItems = $friendsSlice['items'];

    echo view_html('pages/account/index', [
      'profile' => $profile,
      'friends_count' => $friendsSlice['count'],
      'friends' => $friendsSliceItems,
      /**
       * Если количество полученных друзей меньше требуемой величины - подгрузку можно изначально отключить.
       */
      'has_full_friends_list' => (count($friendsSliceItems) < $sliceCountInitial),
    ]);
  }
}