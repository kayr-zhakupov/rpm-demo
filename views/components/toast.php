<?php
/**
 * @var string $type
 * @var string $text
 * @var ?string $class
 */

?>
<div class="toast js-toast toast-<?= $type ?> <?= $class ?? '' ?>">
  <div><?= $text ?></div>
  <br>
  <button type="button" class="js-close-toast">Закрыть</button>
</div>
