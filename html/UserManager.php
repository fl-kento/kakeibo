<?php
require_once('Validator.php');
require_once('Register.php');
class UserManager {
  public function registUser($post_content) {
    $check_regist_content = new Validator();
    $check_regist_content->checkUser($post_content['name'], $post_content['password'], 'register'); 
    if ($check_regist_content->error_message) {
      return $check_regist_content->error_message;
    } else {
      $regist_content = new Register();
      $regist_content->regist($post_content['name'], $post_content['password']);
      return 0;
    }
  }
  public function loginUser($post_content) {
    $check_login_content = new Validator();
    $check_login_content->checkUser($post_content['name'], $post_content['password'], 'login');
    if ($check_login_content->error_message) {
      return $check_login_content->error_message;
    } else {
      return $check_login_content->user_id;
    }
  }
}
?>