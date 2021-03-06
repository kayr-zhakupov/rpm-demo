<?php

namespace App\Profile;

use App\Repo\Profiles;

class ProfilesSliceRequest
{
  protected int $requestedCount;
  protected int $offset;
  protected array $tags;
  protected ?string $friendsOfId;
  protected ?string $mutualFriendsWith;
  //
  protected int $total_count;
  protected array $items;

  public function __construct(array $params)
  {
    $this->requestedCount = $params['count'] ?? 0;
    $this->offset = (int)($params['offset'] ?: 0);
    $this->setTags($params['tags'] ?? null);
    $this->friendsOfId = (string)($params['friends_of_id'] ?? null);
    $this->mutualFriendsWith = (string)($params['friends_with'] ?? null);
    if ($this->friendsOfId === $this->mutualFriendsWith) {
      $this->mutualFriendsWith = null;
    }
  }

  protected function setTags($tags)
  {
    $this->tags = [];

    if (empty($tags)) return;

    if (is_string($tags)) {
      $tags = explode(',', trim($tags));
    }

    if (is_array($tags)) {
      $this->tags = array_unique(array_filter($tags));
    }
  }

  protected function fetchItems()
  {
    if ($this->friendsOfId !== null) {

      $sliceResponse = ($this->mutualFriendsWith !== null)
        ? Profiles::i()->fetchMutualFriendsListSlice($this)
        : (
        ($this->tags)
          ? Profiles::i()->fetchProfilesByTags($this)
          : Profiles::i()->fetchFriendsListSlice($this)
        );

      $this->total_count = $sliceResponse['count'];

      $this->items = $sliceResponse['items'];
      $this->items = Profiles::i()->extendSliceItemsWithMutualFriendsCount($this->items);
      $this->items = Profiles::i()->extendSliceItemsWithTags($this->items);

      return;
    }

    $this->total_count = 0;
    $this->items = [];
  }

  protected function ensureItemsFetched()
  {
    if (!isset($this->items)) {
      $this->fetchItems();
    }

    return $this;
  }

  /**
   * @return int
   */
  public function getTotalCount(): int
  {
    $this->ensureItemsFetched();
    return $this->total_count;
  }

  /**
   * @return array
   */
  public function getItems(): array
  {
    $this->ensureItemsFetched();
    return $this->items;
  }

  public function getItemsLength(): int
  {
    return count($this->getItems());
  }

  /**
   * @return int|mixed
   */
  public function getOffset(): int
  {
    return $this->offset;
  }

  public function tagsToString()
  {
    return implode(',', $this->tags);
  }

  /**
   * @return int|mixed
   */
  public function getRequestedCount()
  {
    return $this->requestedCount;
  }

  /**
   * @return array
   */
  public function getTags(): array
  {
    return $this->tags;
  }

  public function getFriendsOfId()
  {
    return $this->friendsOfId;
  }

  public function getMutualFriendsWith()
  {
    return $this->mutualFriendsWith;
  }
}