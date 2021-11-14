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

    $statement->setFetchMode(\PDO::FETCH_ASSOC);

    if ($transform !== null) {
      $transform($statement, $this->pdo);
    }

    return $statement;
  }

  /**
   * В качестве меры безопасности в результирующей строке запроса используются placeholders вместо значений.
   * @param string $table
   * @param array $values
   * @return string
   */
  public function sqlInsertQuery(string $table, array $values)
  {
    $setPartStr = implode(',', array_map(function (string $column) {
      return sprintf("%s = :%s", $column, $column);
    }, array_keys($values)));

    return 'INSERT INTO `' . $table . '` SET ' . $setPartStr;
  }

  public function mysqlDateTimeFormat(\DateTime $dt): string
  {
    return (clone $dt)->setTimezone(new \DateTimeZone('utc'))->format("Y-m-d H:i:s");
  }

  public function compileColumns(array $columnsMap)
  {
    $columnParts = [];

    foreach ($columnsMap as $alias => $column) {
      $columnParts[] = $column . ' AS ' . $alias;
    }

    return implode(',', $columnParts);
  }
}