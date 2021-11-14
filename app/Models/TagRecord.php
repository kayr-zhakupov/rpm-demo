<?php

namespace App\Models;

use App\Foundation\AbstractModel;

/**
 * @property int $id
 * @property string $owner_id
 * @property string $name
 * @property string $created_at
 *
 * extended:
 * @property string $target_id
 */
class TagRecord extends AbstractModel
{

}