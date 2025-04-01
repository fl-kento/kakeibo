<?php
require_once('AbstractValidator.php');
require_once('UserRegistrationValidator.php');
require_once('UserLoginValidator.php');
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
    $user_registration_validator = new UserRegistrationValidator();
    if ($user_registration_validator->validate($post_content)) {
      $register = new Register();
      $register->regist($post_content['name'], $post_content['password']);
      return 0;
    } else {
      return $user_registration_validator->getErrorMessages();
    }
  }
  public function loginUser($post_content) {
    $user_login_validator = new UserLoginValidator();
    if ($user_login_validator->validate($post_content)) {
      return $user_login_validator->user_id;
    } else {
      return $user_login_validator->getErrorMessages();
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