<?php
/**
 * @see \App\Controllers\IndexController::accountIndex()
 *
 * @var \App\Models\ProfileData $profile
 */

?>
<?= view_html('core/head') ?>
<body>

<div>
  <div>
    <img
      src="<?= $profile->photo_400_orig ?>"
      alt="Мой аватар"
    >
  </div>
  <div><?= $profile->displayName() ?></div>
</div>

</body>
