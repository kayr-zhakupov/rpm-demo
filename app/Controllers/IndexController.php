<?php

namespace App\Controllers;

use App\Middleware\Auth;
use App\Profile\ProfilesSliceRequest;
use App\Repo\Profiles;
use App\Repo\Tags;
use App\Views\ProfilesCatalogView;

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
    return $this->userProfile(Auth::i()->getCurrentUserId());
  }

  public function userProfile(string $id)
  {
    $sliceCountInitial = config('friends_slice_count_initial');
    $currentUserId = Auth::i()->getCurrentUserId();

    $profilesSliceRequest = new ProfilesSliceRequest([
      'count' => $sliceCountInitial,
      'offset' => 0,
      'tags' => [],
      'friends_of_id' => $currentUserId,
      'friends_with' => $id,
    ]);
    $catalogView = (new ProfilesCatalogView($profilesSliceRequest));

    $isMyAccount = ($id === null);

    $profile = Profiles::i()->fetchProfileById($id);

    $allMyTags = Tags::i()->getAllMyTags();
    $profileTags = $isMyAccount ? [] : Tags::i()->tagsForProfile($id);

    return view_html('pages/account/index', [
      'profile' => $profile,
      'session' => Auth::i()->ensureCurrentSession(),
      'profiles_catalog_view' => $catalogView,
      'all_tags' => $allMyTags,
      'profile_tags' => $profileTags,
    ]);
  }
}