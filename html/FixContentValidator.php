<?php
class FixContentValidator extends AbstractValidator {
  public function validate(array $post_data) {
    if (empty($post_data['content'])) {
      $this->error_messages['content'] = '内容を入力してください';
    } elseif (mb_strlen($post_data['content']) > 20) {
      $error_messages['length'] = "内容が長すぎます";
    }
    if (empty($post_data['money'])) {
      $this->error_messages['money'] = '金額を入力してください';
    } elseif (!preg_match(' /^[0-9]+$/', $post_data['money'])) {
      $this->error_messages['int'] = "金額は半角数字で入力してください";
    } elseif (strlen($post_data['money']) > 6) {
      $this->error_messages['big'] = "金額が大きすぎます";
    }
    if (empty($post_data['date'])) {
      $this->error_messages['payment_date'] = '支払日を入力してください';
    } elseif (1 > $post_data['date'] or $post_data['date'] > 31) {
      $this->error_messages['Incorrect_format'] = "正しい日にちを入力してください";
    }
    if (!empty($this->error_messages)) {
      $this->is_valid = False;
    }
    return  $this->is_valid;
  } 
}
?>