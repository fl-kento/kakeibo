<?php
require_once('Validator.php');
require_once('Register.php');
require_once('Database.php');
class UserManager {
  public function checkLogin() {
    if (!isset($_SESSION['id']) || $_SESSION['time'] + 3600 < time()) {
      header('Location: ../top.php');
      exit();
    } else {
      $_SESSION['time'] = time();
    }
  }
  public function registUser($post_content) {
    $validator = new Validator();
    $validator->checkUser($post_content['name'], $post_content['password'], 'register'); 
    if ($validator->error_message) {
      return $validator->error_message;
    } else {
      $register = new Register();
      $register->regist($post_content['name'], $post_content['password']);
      return 0;
    }
  }
  public function loginUser($post_content) {
    $validator = new Validator();
    $validator->checkUser($post_content['name'], $post_content['password'], 'login');
    if ($validator->error_message) {
      return $validator->error_message;
    } else {
      return $validator->user_id;
    }
  }
  public function getName($user_id) {
    $db = new Database();
    $sql = 'SELECT name FROM user WHERE id = :id';
    $params = [':id' => $user_id];
    $user = $db->fetch($sql, $params);
    return $user['name'];
  }
}
?>