<?php

namespace App\Models;

use App\Foundation\AbstractModel;

/**
 * @property string $first_name
 * @property int $id
 * @property string $last_name
 * @property bool $can_access_closed
 * @property bool $is_closed
 * @property ?string $photo_50
 * @property ?string $photo_100
 * @property ?string $photo_200
 * @property bool $online
 *
 * extended:
 * @property ?\App\Models\TagRecord[] $tags
 */
class ProfileData extends AbstractModel
{
  public function displayName(): string
  {
    return implode(' ', array_filter([$this->first_name, $this->last_name]));
  }
}