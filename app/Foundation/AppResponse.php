<?php

namespace App\Foundation;

class AppResponse
{
  protected int $status;

  /**
   * @var mixed|null
   */
  protected $body;

  public function __construct(int $status = 200, $body = null)
  {
    $this->status = $status;
    $this->body = $body;
  }

  /**
   * @return int
   */
  public function getStatus(): int
  {
    return $this->status;
  }

  /**
   * @param int $status
   */
  public function setStatus(int $status)
  {
    $this->status = $status;
    return $this;
  }

  /**
   * @return mixed|null
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * @param mixed|null $body
   */
  public function setBody($body)
  {
    $this->body = $body;
    return $this;
  }

  public function renderBody(): string
  {
    if (is_array($this->body)) {
      return json_encode($this->body);
    } elseif (is_scalar($this->body)) {
      return (string) $this->body;
    }

    return '';
  }

  protected function detectContentType(): string
  {
    if (is_array($this->body)) {
      return 'application/json';
    }

    return 'text/html';
  }

  public function sendContentType()
  {
    header(sprintf(
      'Content-Type: %s; charset=utf-8', $this->detectContentType()
    ), true);
  }
}