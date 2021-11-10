<?php

namespace App\Vk;

use App\Foundation\Fetch;

/**
 * @link https://vk.com/dev/authcode_flow_user
 */
class VkAuth
{
  private string $clientId;
  private string $redirectUri;

  public function __construct(array $options = [])
  {
    $this->clientId = $options['client_id'] ?? app()->env('vk_client_id');
    $this->redirectUri = $options['redirect_uri'] ?? app()->appUrl('authorize/vk');
  }

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
        'client_id' => $this->clientId,
        'redirect_uri' => $this->redirectUri,
        // запрос доступа к списку друзей
        'scope' => 2,
      ]);
  }

  protected function getObtainTokenLink(string $code): string
  {
    return 'https://oauth.vk.com/access_token?' .
      http_build_query([
        'client_id' => $this->clientId,
        'client_secret' => app()->env('vk_client_secret'),
        'redirect_uri' => $this->redirectUri,
        'code' => $code,
      ]);
  }

  public function fetchAccessToken(string $code): string
  {
    $response = (new Fetch($this->getObtainTokenLink($code)))->request();

    dd($response);
  }
}