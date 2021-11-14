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
      return $this->accountIndex();
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
    $profileSlice = $isMyAccount
      ? Profiles::i()->fetchFriendsOrTaggedProfilesListSlice($sliceCountInitial)
      : Profiles::i()->fetchMutualFriendsListSlice(null, $id, $sliceCountInitial);
    $profilesSliceItems = $profileSlice['items'];
    $allMyTags = Tags::i()->getAllMyTags();
    $profileTags = $isMyAccount ? [] : Tags::i()->tagsForProfile($id);

    return view_html('pages/account/index', [
      'profile' => $profile,
      'session' => Auth::i()->ensureCurrentSession(),
      'title' => "Все друзья",
      'total_count' => $profileSlice['count'],
      'profiles_slice_items' => $profilesSliceItems,
      'has_full_list' => (count($profilesSliceItems) < $sliceCountInitial),
      'all_tags' => $allMyTags,
      'profile_tags' => $profileTags,
    ]);
  }
}