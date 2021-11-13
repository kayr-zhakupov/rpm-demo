<?php

namespace App\Vk;

use App\Foundation\CurlResponse;
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

  public static function make(): VkApi
  {
    return new VkApiMock();
  }

  public function endpoint(string $method, array $params = []): string
  {
    return 'https://api.vk.com/method/' . $method . '?' . http_build_query(array_merge([
        'access_token' => $this->accessToken,
        'v' => '5.131',
      ], $params));
  }

  public function fetchMethod(string $method, array $params = []): CurlResponse
  {
    $response = (new Fetch(
      $this->endpoint($method, $params)
    ))
      ->request();

    if ($errorMessage = $response->getNestedValue('error.error_msg')) {
      die($errorMessage);
    }

    return $response;
  }

  /**
   * @link https://vk.com/dev/friends.get
   * @param array|string $fields
   * @param array $params
   * [
   ** order: ?string, count: ?int, offset: ?int
   * ]
   * @return array
   * [
   ** count: int, items: array,
   * ]
   */
  public function fetchFriendsList($fields = '', array $params = []): array
  {
    if (is_array($fields)) $fields = implode(',', $fields);

    /**
     * Почему такая странная запись сложения массивов: поле fields задаётся только аргументом $fields и не может быть
     * перезаписано; поле orders по умолчанию устанавливается в 'hints', но может быть переопределено.
     */
    $response = $this->fetchMethod('friends.get', [
        'fields' => $fields,
      ] + $params + [
        'order' => 'hints',
      ]);

    $response->okOrThrow();

    return $response->getNestedValue('response');
  }

  /**
   * @link https://vk.com/dev/users.get
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