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

  public function isOk(): bool
  {
    return ($this->status === 200);
  }

  public function okOrThrow(): bool
  {
    if ($this->isOk()) return true;

    throw new \Exception('Response status ' . $this->status);
  }

  public function getNestedValue(string $path, $default = null)
  {
    try {
      $decoded = json_decode($this->rawBody, true);

      if ($decoded === false) throw new \Exception(json_last_error_msg());

      return arr_get($decoded, $path, $default);

    } catch (\Throwable $e) {
      error_log($e);
      die("Response decode error");
    }
  }
}