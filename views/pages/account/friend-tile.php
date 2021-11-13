<?php
/**
 * @var ProfileData $profile
 */

use App\Models\ProfileData;

$friend = new ProfileData($profile);
$displayName = $friend->displayName();
?>

<div class="friend-tile">
  <div class="friend-avatar-wrap">
    <img
      src="<?= $friend->photo_100 ?>"
      alt="<?= sprintf('Аватар пользователя %s', $displayName) ?>"
    >
  </div>
  <div class="friend-tile-main">
    <div><span><?= $displayName ?></span> <small><?= $friend->online ? 'онлайн' : '' ?></small></div>
  </div>
</div>