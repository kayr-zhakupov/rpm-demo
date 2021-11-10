<?php
/**
 * @see \App\Controllers\AuthController::index()
 */

use App\Vk\VkApi;
use App\Vk\VkAuth;

$vkAuth = new VkAuth();

{
//  $vkApi = new VkApi();
//  $vkApi->fetchFriendsList();
}

?>
<?= view_html('core/head') ?>
<body>

<?= $vkAuth->renderSignButton() ?>

</body>
