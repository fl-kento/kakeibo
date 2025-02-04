<?php
class Database {
  private $db;
  public function __construct() {
    $this->db = new PDO('mysql:dbname=money_management;host=127.0.0.1;charset=utf8', 'root', '');
  }
  public function checkType($sql, $params = []) {
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
    return $content;
  }
  public function query($sql, $params = []) {
    $checked_content = $this->checkType($sql, $params);
    $checked_content->execute();
    return $checked_content->fetchAll(PDO::FETCH_ASSOC);
  }
  public function fetch($sql, $params = []) {
    $content = $this->query($sql, $params);
    if ($content) {
      return $content[0];
    } else {
      return false;
    }
  }
  public function execute($sql, $params = []) {
    $checked_content = $this->checkType($sql, $params);
    $checked_content->execute();
  }
}
?>