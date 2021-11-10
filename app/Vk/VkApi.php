<?php

namespace App\Vk;

use App\Foundation\Fetch;

class VkApi
{
  public function endpoint(string $method, array $params = []): string
  {
    $accessToken = $_GET['code'] ?? '';

    return 'https://api.vk.com/method/' . $method . '?' . http_build_query(array_merge([
        'access_token' => $accessToken,
        'v' => '5.131',
      ], $params));
  }

  public function fetchMethod(string $method)
  {
    return (new Fetch(
      $this->endpoint($method)
    ))
      ->request();
  }

  /**
   * @link https://vk.com/dev/friends.get
   * @throws \Exception
   */
  public function fetchFriendsList()
  {
    $response = $this->fetchMethod('friends.get');
    dd($response);
    $response->okOrThrow();

    dd($response);
  }
}