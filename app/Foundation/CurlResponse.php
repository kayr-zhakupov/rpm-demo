<?php

namespace App\Foundation;

class CurlResponse
{
  protected int $status;
  protected string $rawBody;
  protected array $curlInfo;

  public function __construct($status, $rawBody, $curlInfo)
  {
    $this->status = (int)($status ?: 0);
    $this->rawBody = (string)$rawBody;
    $this->curlInfo = $curlInfo;
  }

  public function okOrThrow(): bool
  {
    if ($this->status === 200) return true;

    throw new \Exception('Response status ' . $this->status);
  }
}