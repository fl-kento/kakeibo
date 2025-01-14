<?php
class Database {
  private $db;
  public $connection_error;
  public function __construct() {
    try {
      $this->db = new PDO('mysql:dbname=money_management;host=127.0.0.1;charset=utf8', 'root', '');
    } catch (PDOException $e) {
      $this->connection_error= 'データベースに接続できませんでした。';
    }
  }
  public function query($sql, $params = []) {
    $content = $this->db->prepare($sql);
    foreach ($params as $key => &$value) {
      if (is_int($value)) {
      $type = PDO::PARAM_INT;
      } elseif (is_bool($value)) {
      $type = PDO::PARAM_BOOL;
      } elseif (is_null($value)) {
      $type = PDO::PARAM_NULL;
      } else {
      $type = PDO::PARAM_STR;
      }
      $content->bindParam($key, $value, $type);
    }
    $content->execute();
    return $content->fetchAll(PDO::FETCH_ASSOC);
  }
  public function fetch($sql, $params = []) {
    $content = $this->query($sql, $params);
    return $content ? $content[0] : false;
  }
  public function execute($sql, $params = []) {
    $content = $this->db->prepare($sql);
    foreach ($params as $key => &$value) {
      if (is_int($value)) {
      $type = PDO::PARAM_INT;
    } elseif (is_bool($value)) {
      $type = PDO::PARAM_BOOL;
    } elseif (is_null($value)) {
      $type = PDO::PARAM_NULL;
    } else {
      $type = PDO::PARAM_STR;
    }
      $content->bindParam($key, $value, $type);
    }
    $content->execute();
  }
}
?>