<?php
/**
 * @see \App\Controllers\AuthController::index()
 */

use App\Vk\VkAuth;

$vkAuth = new VkAuth();

?>
<?= view_html('core/head') ?>
<body>
<div style="display: flex;flex-direction: column;align-items: center;justify-content: center;height: 100%;">

<?= $vkAuth->renderSignButton() ?>

</div>
</body>
