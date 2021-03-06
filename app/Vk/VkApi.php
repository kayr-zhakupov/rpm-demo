<?php

namespace App\Vk;

use App\Exceptions\AuthorizationFailedException;
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

    if ($errorCode = $response->getNestedValue('error.error_code')) {
      if ($errorCode === 5) {
        throw new AuthorizationFailedException();
      }

      $message = 'Error ' . $errorCode . ': ' . $response->getNestedValue('error.error_msg');
      error_log($message);
      die($message);
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
   * @param $sourceId
   * @param $targetId
   * @param array $params
   * @return mixed
   * @return array
   * [
   ** count: int, ids: array,
   * ]
   */
  public function fetchMutualFriendsIds($sourceId, $targetId, array $params = [])
  {
    $response = $this->fetchMethod('friends.getMutual', [
        'source_uid' => $sourceId,
        'target_uids' => $targetId,
      ] + $params);

    return [
      'count' => $response->getNestedValue('response.0.common_count'),
      'ids' => $response->getNestedValue('response.0.common_friends'),
    ];
  }

  public function fetchMutualFriendsMultiple($sourceId, array $targetIds, array $params = [])
  {
    $response = $this->fetchMethod('friends.getMutual', [
        'source_uid' => $sourceId,
        'target_uids' => implode(',', $targetIds),
      ] + $params);

    return $response->getNestedValue('response');
  }

  /**
   * @param array|string $fields
   */
  public function fetchSingleProfile($id, $fields = ''): ProfileData
  {
    $profiles = $this->fetchProfiles([$id], $fields);

    if (empty($profiles)) throw new \Exception("Пользователь не найден");

    return new ProfileData($profiles[0]);
  }

  /**
   * @link https://vk.com/dev/users.get
   * @param array $ids
   * @param string $fields
   * @return array[]
   * @throws \Exception
   */
  public function fetchProfiles(array $ids, $fields = ''): array
  {
    if (empty($ids)) return [];

    if (is_array($fields)) $fields = implode(',', $fields);

    $response = $this->fetchMethod('users.get', [
      'user_ids' => $ids,
      'fields' => $fields,
    ]);

    return $response->okOrThrow()->getNestedValue('response');
  }
}