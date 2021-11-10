<?php

namespace App\Foundation;

class CurlResponse
{
  protected int $status;
  protected string $rawBody;
  protected array $curlInfo;

  public function __construct(int $status, $rawBody, $curlInfo)
  {

    $this->status = $status;
    $this->rawBody = (string) $rawBody;
    $this->curlInfo = $curlInfo;
  }
}