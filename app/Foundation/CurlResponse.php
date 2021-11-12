<?php

namespace App\Foundation;

class CurlResponse
{
  protected int $status;
  protected string $rawBody;
  protected array $curlInfo;

  protected ?array $jsonBodyDecoded;

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

  public function getJsonDecoded(): ?array
  {
    if (!isset($this->jsonBodyDecoded)) {
      $this->jsonBodyDecoded = json_decode($this->rawBody, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        $this->jsonBodyDecoded = null;
      }
    }

    return $this->jsonBodyDecoded;
  }

  /**
   * @param string $path
   * @param mixed $default
   * @return mixed
   */
  public function getNestedValue(string $path, $default = null)
  {
    return arr_get($this->getJsonDecoded(), $path, $default);
  }
}