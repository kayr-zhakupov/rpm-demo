<?php

namespace App\Vk;

use App\Foundation\Fetch;
use App\Middleware\Auth;
use App\Models\ProfileData;

class VkApi
{
  protected ?string $accessToken;

  public function __construct()
  {
    $this->accessToken = Auth::i()->getCurrentVkAccessToken();
  }

  public function endpoint(string $method, array $params = []): string
  {
    return 'https://api.vk.com/method/' . $method . '?' . http_build_query(array_merge([
        'access_token' => $this->accessToken,
        'v' => '5.131',
      ], $params));
  }

  public function fetchMethod(string $method, array $params = [])
  {
    return (new Fetch(
      $this->endpoint($method, $params)
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

  /**
   * @param array|string $fields
   */
  public function fetchMyProfile($fields = ''): ProfileData
  {
    if (is_array($fields)) $fields = implode(',', $fields);

    $response = $this->fetchMethod('users.get', [
      'fields' => $fields,
    ]);

    $response->okOrThrow();

    return new ProfileData($response->getNestedValue('response.0'));
  }
}