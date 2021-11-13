<?php
/**
 * @var ProfileData|array $profile
 */

use App\Models\ProfileData;
use App\Repo\Routes;

$profile = new ProfileData($profile);
$displayName = $profile->displayName();
?>

<div class="friend-tile">
  <a
    class="__stretched-link"
    href="<?= Routes::i()->user($profile->id) ?>"
  ></a>
  <div class="friend-avatar-wrap">
    <img
      src="<?= $profile->photo_100 ?>"
      alt="<?= sprintf('Аватар пользователя %s', $displayName) ?>"
    >
  </div>
  <div class="friend-tile-main">
    <div><span><?= $displayName ?></span> <small class="__online-badge"><?= $profile->online ? 'онлайн' : '' ?></small></div>
  </div>
</div>