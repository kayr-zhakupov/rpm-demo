<?php
/**
 * @var string $type
 * @var string $text
 * @var ?string $class
 * @var ?bool $is_visible
 */

$is_visible = $is_visible ?? true;
$class = implode(' ', array_filter([
  'toast js-toast',
  "toast-$type",
  $is_visible ? '--show' : null,
  $class ?? null,
]));

?>
<div class="<?= $class ?>">
  <div><?= $text ?></div>
  <br>
  <button type="button" class="js-close-toast">Закрыть</button>
</div>
