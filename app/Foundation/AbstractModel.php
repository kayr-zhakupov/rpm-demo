<?php

namespace App\Foundation;

class AbstractModel
{
  protected array $attributes;

  public function __construct($input)
  {
    $this->attributes = (array) $input;
  }

  public function get(?string $key = null)
  {
    return arr_get($this->attributes, $key);
  }

  public function __get($name)
  {
    return $this->get($name);
  }
}