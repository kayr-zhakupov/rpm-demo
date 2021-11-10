<?php

namespace App;

use PDO;
use Throwable;

/**
 * Синглтон движка.
 */
class Application
{
  protected static Application $instance;

  /**
   * Секретные данные вне VCS
   * @var array
   */
  protected array $env;
  protected array $config;

  public function __construct()
  {
    $this->env = require APP_BASE_PATH . '/env/env.php';
    $this->config = [];
  }

  public static function i()
  {
    if (!isset(static::$instance)) {
      static::$instance = new static();
    }

    return static::$instance;
  }

  /**
   * Отправитель запросов по мотивам исходного кода Laravel.
   * Похожий механизм использовал в собственных framework-free разработках.
   *
   * @param string $sql
   * @param array $bindings
   * @param callable|null $transform
   * @return false|\PDOStatement
   */
  protected function createDbStatement(string $sql, array $bindings = [], ?callable $transform = null)
  {
    $dsn = sprintf(
      "mysql:host=%s;dbname=%s;charset=UTF8",
      $this->env['db']['host'] ?? '',
      $this->env['db']['name'] ?? '',
    );
    $user = $this->env['db']['user'] ?? '';
    $password = $this->env['db']['pass'] ?? '';

    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ];

    $pdo = new PDO($dsn, $user, $password, $options);

    $pdo->prepare("SET NAMES utf8; SET time_zone = 'utc'")->execute();

    $statement = $pdo->prepare($sql);
    foreach ($bindings as $key => $value) {
      $statement->bindValue(
        ":$key",
        $value,
        is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR,
      );
    }

    if ($transform !== null) {
      $transform($statement, $pdo);
    }

    return $statement;
  }

  protected function dbExecuteStatement(string $sql, array $bindings = [], ?callable $transform = null)
  {
    $statement = $this->createDbStatement($sql, $bindings, $transform);

    try {
      $statement->execute();
    } catch (Throwable $e) {
      $this->handleError($e);
    }

    return $statement;
  }

  public function dbExecuteAffectingQuery(string $sql, array $bindings = [], ?callable $transform = null): int
  {
    return $this->dbExecuteStatement($sql, $bindings, $transform)->rowCount();
  }

  public function dbSelect(string $sql, array $bindings = [], ?callable $transform = null)
  {
    return $this->dbExecuteStatement($sql, $bindings, $transform)->fetchAll();
  }

  protected function handleError(Throwable $e)
  {
    echo $e;
    echo PHP_EOL;
    die();
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

  public function runControllerAndDie($controller)
  {
    if ($controller !== null) {
      $result = $controller();

      if (is_null($result)) {
        // noop
      } elseif (is_array($result)) {
        echo json_encode($result);
      } elseif (is_scalar($result)) {
        echo $result;
      }

      die();
    }

    http_response_code(404);
    die();
  }

  /**
   * @param string|null $key
   * @return array|mixed
   */
  public function config(?string $key = null)
  {
    return ($key === null) ? $this->config : ($this->config[$key] ?? null);
  }
}