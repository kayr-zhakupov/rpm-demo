<?php

namespace App\Controllers;

use App\Middleware\Auth;
use App\Repo\Profiles;
use App\Repo\Tags;

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

  public function accountIndex()
  {
    return $this->userProfile(null);
  }

  /**
   * @param string|int|null $id
   */
  public function userProfile($id)
  {
    $isMyAccount = ($id === null);

    $profile = Profiles::i()->fetchProfileById($id);
    $sliceCountInitial = config('friends_slice_count_initial');
    $friendsSlice = $isMyAccount
      ? Profiles::i()->fetchFriendsListSlice($sliceCountInitial)
      : Profiles::i()->fetchMutualFriendsListSlice(null, $id, $sliceCountInitial);
    $friendsSliceItems = $friendsSlice['items'];
    $allMyTags = Tags::i()->getAllMyTags();
    $profileTags = $isMyAccount ? [] : Tags::i()->tagsForProfile($id);

    return view_html('pages/account/index', [
      'profile' => $profile,
      'session' => Auth::i()->ensureCurrentSession(),
      'friends_count' => $friendsSlice['count'],
      'friends' => $friendsSliceItems,
      'has_full_friends_list' => (count($friendsSliceItems) < $sliceCountInitial),
      'all_tags' => $allMyTags,
      'profile_tags' => $profileTags,
    ]);
  }
}