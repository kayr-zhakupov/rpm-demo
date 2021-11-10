<?php

namespace App\Vk;

/**
 * @link https://vk.com/dev/implicit_flow_user
 */
class VkAuth
{
  /**
   * @return string
   */
  public function renderSignButton(): string
  {
    ob_start();

    ?>
    <a
      href="<?= $this->getSignLink() ?>"
    >Авторизоваться с ВК</a>
    <?php

    return ob_get_clean();
  }

  protected function getSignLink(): string
  {
    return 'https://oauth.vk.com/authorize?' .
      http_build_query([
        'client_id' => app()->env('vk_client_id'),
        'redirect_uri' => app()->appUrl(),
      ]);
  }
}