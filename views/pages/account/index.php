<?php
/**
 * @see \App\Controllers\IndexController::accountIndex()
 *
 * @var \App\Models\ProfileData $profile
 * @var int $friends_count
 * @var array[] $friends
 */

use App\Models\ProfileData;

?>
<?= view_html('core/head', [
  'head_cb' => function () {
    ?>
    <link href="<?= app()->styleUrl('gen/account-index.css?v=0.0.1') ?>" rel="stylesheet"/>
    <script src="<?= app()->scriptUrl('account-index.js') ?>"></script>
    <?php
  },
]) ?>
<body class="account-index-page">

<div class="account-index-container">

  <div class="account-index-profile">

    <div>
      <img
        src="<?= $profile->photo_200 ?>"
        alt="Мой аватар"
      >
    </div>

    <div><?= $profile->displayName() ?></div>
  </div>

  <div class="account-index-friends-catalog">

    <div class="friends-catalog-head">
      <span>Все друзья</span> <i><?= $friends_count ?></i>
    </div>

    <div class="friends-list-scrollable js-infinite-scroll">

      <?php
      foreach ($friends as $friend):
        $friend = new ProfileData($friend);
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

      <?php endforeach; ?>

    </div>

  </div>
</div>

</body>
