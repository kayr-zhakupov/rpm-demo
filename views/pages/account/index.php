<?php
/**
 * @see \App\Controllers\IndexController::userProfile()
 *
 * @var \App\Models\VkAccessTokenRecord $session
 * @var \App\Models\ProfileData $profile
 * @var int $friends_count
 * @var array[] $friends
 * @var bool $has_full_friends_list
 * @var \App\Models\TagRecord[] $all_tags
 * @var \App\Models\TagRecord[] $profile_tags
 */

use App\Repo\Routes;

$isMyAccount = ($session && ((string)$session->user_id === (string)$profile->id));

?>
<?= view_html('core/head', [
  'head_cb' => function () use ($profile) {
    ?>
    <link href="<?= app()->styleUrl('gen/account-index.css?v=0.0.1') ?>" rel="stylesheet"/>

    <script src="<?= app()->scriptUrl('base.js') ?>"></script>
    <script src="<?= app()->scriptUrl('account-index.js') ?>"></script>
    <script src="<?= app()->scriptUrl('toasts.js') ?>"></script>
    <script src="<?= app()->scriptUrl('tags.js') ?>"></script>

    <script>window.App = <?= json_encode([
        'infinite_scroll_threshold' => config('load_more_offset'),
        'ajax_get_friends_slice_url' => app()->appUrl('ajax/friends'),
        'friends_slice_count_next' => config('friends_slice_count_next'),
        //
        'ajax_tags_submit_url' => Routes::i()->ajaxTags(),
        'tags_target_user_id' => $profile->id,
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
    <br>

    <?php if (!$isMyAccount) : ?>

      <?= view_html('pages/account/tags', compact('all_tags', 'profile_tags')) ?>

      <hr>

      <a href="<?= Routes::i()->my() ?>">Вернуться к Моему аккаунту</a>
    <?php endif; ?>
  </div>

  <div class="account-index-friends-catalog">

    <div class="friends-catalog-head">
      <span>Все друзья</span> <i><?= $friends_count ?></i>
    </div>

    <div
      class="friends-list-scrollable js-infinite-scroll"
      data-count="<?= count($friends) ?>"
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

<div class="toast-container js-toast-container">
  <?= view_html('components/toast', [
    'type' => 'error',
    'text' => 'Ошибка сервера',
    'class' => 'js-general-server-error',
    'is_visible' => false,
  ]) ?>
</div>

</body>
