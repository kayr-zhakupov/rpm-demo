<?php

namespace App\Exceptions;

use Throwable;

class AuthorizationFailedException extends HttpException
{
  public function __construct($message = "", $code = 0, Throwable $previous = null)
  {
    parent::__construct(404, $message, $code, $previous);
  }
}