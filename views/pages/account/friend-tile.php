<?php
/**
 * @var ProfileData|array $profile
 */

use App\Models\ProfileData;
use App\Repo\Routes;

$profile = new ProfileData($profile);
$displayName = $profile->displayName();
$mutualFriendsCount = $profile->common_count;
?>

<div class="friend-tile js-friend-tile" style="height: <?= config('friend_tile_height', 'unset') ?>">
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
    <div><span><?= $displayName ?></span> <small class="__online-badge"><?= $profile->online ? 'онлайн' : '' ?></small>
    </div>

    <div class="__tags">
      <?php foreach ($profile->tags ?? [] as $tagRecord): ?>
        <div class="__tag"><?= $tagRecord->name ?></div>
      <?php endforeach; ?>
    </div>

    <?php if ($mutualFriendsCount): ?>
      <div><small><?= sprintf("Общих друзей: %d", $mutualFriendsCount) ?></small></div>
    <?php endif; ?>

  </div>

</div>