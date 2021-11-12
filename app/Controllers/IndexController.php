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
    $friends = Profiles::i()->fetchFriendsListSlice();

    echo view_html('pages/account/index', compact('profile', 'friends'));
  }
}