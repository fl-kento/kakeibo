<?php
require_once('Database.php');
class Register {
  public $db;
  public function __construct() {
    $this->db = new Database();
  }
  public function regist($name, $password) {
    $sql = 'SELECT id FROM user ORDER BY id DESC LIMIT 1';
    $latest_id = $this->db->fetch($sql);
    $latest_id['id'] += 1;
    $sql = 'INSERT INTO user (id, name, password) VALUE (:id, :name, :password)';
    $params = [':id' => $latest_id['id'], ':name' => $name, ':password' => $password];
    $this->db->execute($sql, $params);
  } 
}
?>