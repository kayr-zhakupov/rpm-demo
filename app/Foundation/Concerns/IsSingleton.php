<?php

namespace App\Foundation\Concerns;

trait IsSingleton
{
//  private static $instance;

  /**
   * @return static
   * @noinspection PhpFieldAssignmentTypeMismatchInspection
   */
  public static function i()
  {
    if (!isset(static::$instance)) {
      static::$instance = new static();
    }

    return static::$instance;
  }
}