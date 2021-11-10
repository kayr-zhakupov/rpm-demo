<?php

namespace App\Foundation;

use PDO;

class DB
{
  protected PDO $pdo;

  protected array $params;

  public function __construct(array $params)
  {
    $this->params = $params;
  }

  public function pdo(): PDO
  {
    if (!isset($this->pdo)) {

      try {
        $dsn = sprintf(
          "mysql:host=%s;dbname=%s;charset=UTF8",
          $this->params['host'] ?? '',
          $this->params['name'] ?? '',
        );
        $user = $this->params['user'] ?? '';
        $password = $this->params['pass'] ?? '';

        $options = [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];

        $pdo = new PDO($dsn, $user, $password, $options);

        $pdo->prepare("SET NAMES utf8; SET time_zone = 'utc'")->execute();

        $this->pdo = $pdo;

      } catch (\Throwable $e) {
        error_log($e);
        die("PDO error");
      }
    }

    return $this->pdo;
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
  public function statement(string $sql, array $bindings = [], ?callable $transform = null)
  {
    $statement = $this->pdo()->prepare($sql);
    foreach ($bindings as $key => $value) {
      $statement->bindValue(
        ":$key",
        $value,
        is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR,
      );
    }

    if ($transform !== null) {
      $transform($statement, $this->pdo);
    }

    return $statement;
  }
}