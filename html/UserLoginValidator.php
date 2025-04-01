<?php
class UserLoginValidator extends AbstractValidator {
  public $user_id;
  public function validate(array $post_data) {
    $name = $post_data['name'];
    $password = $post_data['password'];
    if (empty($name)) {
      $this->error_messages['name'] = 'ユーザー名を入力してください';
      $this->is_valid = False;
    }
    if (empty($password)) {
      $this->error_messages['password'] = 'パスワードを入力してください';
      $this->is_valid = False;
    }
    if ($this->is_valid) {
      $sql = 'SELECT id, name, password FROM user WHERE name = :user_name AND password = :pass';
      $params = [':user_name' => $name, ':pass' => $password];
      $result = $this->db->fetch($sql, $params);
      if ($result) {
        $this->user_id = $result['id'];
      } else {
        $this->error_messages['login'] = 'ユーザー名かパスワードが異なっています';
        $this->is_valid = False;
      }
    }
    return $this->is_valid;
  } 
}
?>