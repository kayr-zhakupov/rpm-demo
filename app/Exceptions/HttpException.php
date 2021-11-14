<?php

namespace App\Exceptions;

use Throwable;

class HttpException extends \Exception
{
  protected int $status;

  public function __construct(int $status, $message = "", $code = 0, Throwable $previous = null)
  {
    $this->status = $status;
    parent::__construct($message, $code, $previous);
  }

  /**
   * @return int
   */
  public function getStatus(): int
  {
    return $this->status;
  }
}