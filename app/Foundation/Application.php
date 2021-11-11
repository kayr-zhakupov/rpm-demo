<?php

namespace App\Foundation;

use App\Foundation\Concerns\IsSingleton;
use PDO;
use Throwable;

/**
 * Синглтон движка.
 */
class Application
{
  use IsSingleton;

  private static Application $instance;

  /**
   * Секретные данные вне VCS
   * @var array
   */
  protected array $env;
  protected array $config;

  protected Router $router;
  protected DB $db;

  public function __construct()
  {
    $this->env = require APP_BASE_PATH . '/env/env.php';
    $this->config = [];
    $this->router = new Router();
    $this->db = new DB($this->env['db']);
  }

  /**
   * @param string|null $key
   * @return array|mixed
   */
  public function config(?string $key = null, $default = null)
  {
    return ($key === null) ? $this->config : ($this->config[$key] ?? $default);
  }

  public function env(?string $key = null, $default = null)
  {
    return ($key === null) ? $this->env : ($this->env[$key] ?? $default);
  }

  public function router(): Router
  {
    return $this->router;
  }

  public function db(): DB
  {
    return $this->db;
  }

  public function appUrl(...$parts): string
  {
    return implode('/', array_filter(array_merge([$this->env['url'] ?? ''], $parts)));
  }

  public function styleUrl(string $relName): string
  {
    return $this->appUrl('css', $relName);
  }

  public function scriptUrl(string $relName): string
  {
    return $this->appUrl('js', $relName);
  }
}