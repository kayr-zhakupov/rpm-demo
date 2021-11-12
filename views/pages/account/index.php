<?php
/**
 * @see \App\Controllers\IndexController::accountIndex()
 *
 * @var \App\Models\ProfileData $profile
 */

?>
<?= view_html('core/head', [
  'head_cb' => function () {
    ?>
    <link href="<?= app()->styleUrl('gen/account-index.css?v=0.0.1') ?>" rel="stylesheet"/>
    <?php
  },
]) ?>
<body>

<div class="account-index-container">
  <div class="account-index-profile">

    <div>
      <img
        src="<?= $profile->photo_400_orig ?>"
        alt="Мой аватар"
      >
    </div>

    <div><?= $profile->displayName() ?></div>
  </div>

  <div class="account-index-friends-catalog">
FRIENDS
  </div>
</div>

</body>
