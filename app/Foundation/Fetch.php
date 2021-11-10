<?php

namespace App\Foundation;

class Fetch
{
  protected string $url;
  protected string $method;

  /**
   * @param string $url
   * @param string $method
   */
  public function __construct(string $url, string $method = 'get')
  {
    $this->url = $url;
    $this->method = strtolower($method);
  }

  public function request(): CurlResponse
  {
    $ch = curl_init($this->url);
    $rawHeaders = [];

    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER => true,
      CURLOPT_CUSTOMREQUEST => $this->method,
      CURLOPT_HEADERFUNCTION => function ($ch, $rawHeader) use (&$rawHeaders) {
        $rawHeaders[] = trim($rawHeader);
        return strlen($rawHeader);
      },
    ]);

    $result = curl_exec($ch);
    $info = curl_getinfo($ch);
    if (!is_array($info)) {
      throw new \Exception('Curl error');
    }

    $headerLength = $info['header_size'];
    $body = substr($result, $headerLength);

    // TODO 2021-07-22T20:14:52 error
    if ($error = curl_error($ch)) {
      throw new \Exception('Curl error: ' . $error);
    }
    curl_close($ch);

    // TODO 2021-07-22T20:33:55 headers
    return new CurlResponse($info['http_code'], $body, $info);
  }
}