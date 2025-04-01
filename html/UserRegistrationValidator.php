<?php
class UserRegistrationValidator extends AbstractValidator {
  public function validate(array $post_data) {
    $name = $post_data['name'];
    $password = $post_data['password'];
    if (empty($name)) {
      $this->error_messages['name'] = 'ユーザー名を入力してください';
    } elseif (mb_strlen($name) > 20) {
      $this->error_messages['name'] = '20文字以下で入力してください';
    } else {
      $sql = 'SELECT name FROM user WHERE name = :user_name';
      $params = [':user_name' => $name];
      $result = $this->db->fetch($sql, $params);
      if ($result) {
        $this->error_messages['name'] = 'ユーザー名が既に使用されています';
      }
    }
    if (empty($password)) {
      $this->error_messages['password'] = 'パスワードを入力してください';
    } else {
      if (!preg_match(' /^[a-zA-Z0-9]+$/', $password)) {
        $this->error_messages['password'] = '大文字、小文字、数値で入力してください';
      }
      if (mb_strlen($password) < 10) {
        $this->error_messages['password'] = '10桁以上で入力してください';
      }
    }
    if (!empty($this->error_messages)) {
      $this->is_valid = False;
    }
    return  $this->is_valid;
  }
}
?>