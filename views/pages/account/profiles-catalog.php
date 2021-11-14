<?php
/**
 * @see views/pages/account/index.php:71
 *
 * @var string $title
 * @var int $total_count
 * @var array $profiles_slice_items
 * @var bool $has_full_list
 */
?>

<div class="account-index-profiles-catalog js-profiles-catalog">

  <div class="friends-catalog-head">
    <span>Все друзья</span> <i><?= $total_count ?></i>
  </div>

  <div
    class="friends-list-scrollable js-infinite-scroll"
    data-count="<?= count($profiles_slice_items) ?>"
    data-has-full-list="<?= $has_full_list ?>"
  >

    <?php
    foreach ($profiles_slice_items as $profile):
      echo view_html('pages/account/friend-tile', [
        'profile' => $profile,
      ]);
    endforeach;
    ?>

    <div
      class="__load-more js-load-more js-load-more-before"
      style="height: <?= config('load_more_offset') ?>px"
    >
      <div class="progress-bar"></div>
    </div>

  </div>

</div>
