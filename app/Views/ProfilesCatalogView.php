<?php

namespace App\Views;

class ProfilesCatalogView
{
  protected int $totalCount;
  protected array $items;

  public function __construct(array $slice)
  {
    $this->items = $slice['items'] ?? [];
    $this->totalCount = $slice['count'] ?? $slice['total_count'] ?? count($this->items);
  }

  public function getTitle(): string {
    return 'Все друзья';
  }

  /**
   * @return int
   */
  public function getTotalCount()
  {
    return $this->totalCount;
  }

  /**
   * @return int
   */
  public function getCurrentCount()
  {
    return count($this->items);
  }

  /**
   * @return array
   */
  public function getItems()
  {
    return $this->items;
  }
}