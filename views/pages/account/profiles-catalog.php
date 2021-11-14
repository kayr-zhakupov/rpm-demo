<?php
/**
 * @see views/pages/account/index.php:71
 *
 * @var \App\Views\ProfilesCatalogView $profiles_catalog_view
 */
?>

<div class="account-index-profiles-catalog js-profiles-catalog">

  <div class="friends-catalog-head">
    <span><?= $profiles_catalog_view->getTitle() ?></span> <i><?= $profiles_catalog_view->getTotalCount() ?></i>
  </div>

  <div
    class="friends-list-scrollable js-infinite-scroll"
    data-count="<?= $profiles_catalog_view->getCurrentCount() ?>"
    data-has-full-list="<?= $profiles_catalog_view->getCurrentCount() === $profiles_catalog_view->getTotalCount() ?>"
  >

    <?php
    foreach ($profiles_catalog_view->getItems() as $profile):
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
