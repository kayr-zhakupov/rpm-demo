<?php
/**
 * @see \App\Controllers\AuthController::index()
 */

use App\Vk\VkAuth;

$vkAuth = new VkAuth();

?>
<?= view_html('core/head', [
  'title' => 'Вход',
]) ?>
<body>

<?= $vkAuth->renderSignButton() ?>

</body>
