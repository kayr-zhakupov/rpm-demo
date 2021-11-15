<?php
/**
 * @see views/pages/account/index.php:68
 *
 * @var \App\Views\ProfilesCatalogView $profiles_catalog_view
 * @var \App\Models\ProfileData $profile
 */
?>

<?php if ($profile->deactivated): ?>

  <div>Страница удалена или заблокирована</div>

<?php else: ?>

  <div class="account-index-profiles-catalog">

    <?= $profiles_catalog_view->renderHead() ?>

    <div
      class="profiles-list-scrollable js-infinite-scroll"
      data-count="<?= $profiles_catalog_view->getRequest()->getItemsLength() ?>"
      data-has-full-list="<?=
      $profiles_catalog_view->getRequest()->getItemsLength() === $profiles_catalog_view->getRequest()->getTotalCount()
      ?>"
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

<?php endif; ?>
