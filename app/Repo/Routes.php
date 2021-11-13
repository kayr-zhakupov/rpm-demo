<?php

namespace App\Repo;

use App\Foundation\Concerns\IsSingleton;

class Routes
{
  use IsSingleton;

  private static Routes $instance;

  public function user($id)
  {
    return app()->appUrl('u/' . $id);
  }
}