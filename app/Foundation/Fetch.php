<?php

namespace App\Foundation;

class Fetch
{
  protected string $url;
  protected string $method;
  protected array $headers;

  /**
   * @param string $url
   * @param string $method
   */
  public function __construct(string $url, string $method = 'get', array $headers = [])
  {
    $this->url = $url;
    $this->method = strtolower($method);
    $this->headers = $headers;
  }

  public function request(): CurlResponse
  {
    $ch = curl_init($this->url);

    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HEADER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
      CURLOPT_CUSTOMREQUEST => strtoupper($this->method),
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