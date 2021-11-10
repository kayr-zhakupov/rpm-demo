<?php
/**
 * @see \App\Controllers\AuthController::index()
 */

use App\Vk\VkAuth;

$vkAuth = new VkAuth();

?>
<?= view_html('core/head') ?>
<body>

<?= $vkAuth->renderSignButton() ?>

</body>
