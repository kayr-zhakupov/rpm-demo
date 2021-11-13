<?php
/**
 * @see \App\Controllers\IndexController::accountIndex()
 *
 * @var \App\Models\ProfileData $profile
 * @var int $friends_count
 * @var array[] $friends
 * @var bool $has_full_friends_list
 */

?>
<?= view_html('core/head', [
  'head_cb' => function () {
    ?>
    <link href="<?= app()->styleUrl('gen/account-index.css?v=0.0.1') ?>" rel="stylesheet"/>
    <script src="<?= app()->scriptUrl('account-index.js') ?>"></script>
    <script>window.App = <?= json_encode([
        'infinite_scroll_threshold' => config('load_more_offset'),
        'ajax_get_friends_slice_url' => app()->appUrl('ajax/friends'),
        'friends_slice_count_next' => config('friends_slice_count_next'),
      ]) ?></script>
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

    <div
      class="friends-list-scrollable js-infinite-scroll"
      data-offset="<?= count($friends) ?>"
      data-has-full-list="<?= $has_full_friends_list ?>"
    >

      <?php
      foreach ($friends as $friend):
        echo view_html('pages/account/friend-tile', [
          'profile' => $friend,
        ]);
      endforeach;
      ?>

      <div class="__load-more js-load-more js-load-more-before" style="height: <?= config('load_more_offset') ?>px">
        <div class="progress-bar"></div>
      </div>

    </div>

  </div>
</div>

</body>
