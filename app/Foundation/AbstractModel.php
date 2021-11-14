<?php

namespace App\Foundation;

class AbstractModel
{
  /**
   * @param null|\App\Foundation\AbstractModel|array $input
   */
  public function __construct($input = null)
  {
    if ($input instanceof AbstractModel) {
      $input = $input->toArray();
    }

    foreach ($input ?? [] as $key => $value) {
      $this->{$key} = $value;
    }
  }

  public function toArray(): array
  {
    dd(__METHOD__);
  }

  public function get(?string $key = null, $default = null)
  {
    return property_exists($this, $key) ? $this->{$key} : $default;
  }

  public function __get($name)
  {
    return $this->get($name);
  }
}