<?php
require_once('Database.php');
class CheckValidation {
  public $error_message = [];
  public $user_id;
  public $db;
  public function __construct() {
    $this->db = new Database();
    if (!empty($this->db->connection_error)) {
      $this->error_message['db'] = $this->db->connection_error;
    }
  }
  public function check($post_content, $type) {
    if ($type === 'login') {
      if (empty($post_content['name'])) {
        $this->error_message['name'] = 'ユーザー名を入力してください';
      }
      if (empty($post_content['password'])) {
        $this->error_message['password'] = 'パスワードを入力してください';
      }
      if (empty($this->error_message)) {
        $sql = 'SELECT id, name, password FROM user WHERE name = :user_name AND password = :pass';
        $params = [':user_name' => $post_content['name'], ':pass' => $post_content['password']];
        $result = $this->db->fetch($sql, $params);
        $result ? $this->user_id = $result['id'] : $this->error_message['login'] = 'ユーザー名かパスワードが異なっています';
      }
    } elseif ($type === 'register') {
      if (empty($post_content['name'])) {
        $this->error_message['name'] = 'ユーザー名を入力してください';
      } elseif (mb_strlen($post_content['name']) > 20) {
        $this->error_message['name'] = '20文字以下で入力してください';
      } else {
        $sql = 'SELECT name FROM user WHERE name = :user_name';
        $params = [':user_name' => $post_content['name']];
        $result = $this->db->fetch($sql, $params);
        $result ? $this->error_message['name'] = 'ユーザー名が既に使用されています' : '';
      }
      if (empty($post_content['password'])) {
        $this->error_message['password'] = 'パスワードを入力してください';
      } else {
        if (!preg_match(' /^[a-zA-Z0-9]+$/', $post_content['password'])) {
          $this->error_message['password'] = '大文字、小文字、数値で入力してください';
        }
        if (mb_strlen($post_content['password']) < 10) {
          $this->error_message['password'] = '10桁以上で入力してください';
        }
      }
      if (empty($this->error_message)) {
        $sql = 'SELECT id FROM user ORDER BY id DESC LIMIT 1';
        $latest_id = $this->db->fetch($sql);
        $latest_id['id'] += 1;
        $sql = 'INSERT INTO user (id, name, password) VALUE (:id, :name, :password)';
        $params = [':id' => $latest_id['id'], ':name' => $post_content['name'], ':password' => $post_content['password']];
        $result = $this->db->execute($sql, $params);
      }
    }
  }
}
?>