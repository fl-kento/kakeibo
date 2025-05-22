<?php
class ContentValidator extends AbstractValidator {
  public function validate(array $post_data) {
    if (empty($post_data['kinds'])) {
      $this->error_messages['kinds'] = '種類を選んでください';
    }
    if (empty($post_data['money'])) {
      $this->error_messages['money'] = '金額を入力してください';
    } elseif (!preg_match(' /^[0-9]+$/', $post_data['money'])) {
      $this->error_messages['int'] = "金額は半角数字で入力してください";
    } elseif (strlen($post_data['money']) > 6) {
      $this->error_messages['big'] = "金額が大きすぎます";
    }
    if (empty($post_data['date'])) {
      $this->error_messages['date'] = '日付を選んでください';
     } elseif (2027 < $post_data['date'] || $post_data['date'] < 2024) {
      $this->error_messages['date'] = '2024~2026年で入力してください';
    }
    if (!empty($this->error_messages)) {
      $this->is_valid = False;
    }
    return $this->is_valid;
  }
}
?>