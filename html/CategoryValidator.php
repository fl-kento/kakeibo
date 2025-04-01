<?php
class CategoryValidator extends AbstractValidator {
  public function validate($post_data) {
    if (empty($post_data['name'])) {
      $this->error_messages['name'] = 'カテゴリ名を入力してください';
    } elseif (mb_strlen($post_data['name']) > 10) {
      $this->error_messages['length'] = "カテゴリ名が長すぎます";
    }
    if (!empty($this->error_messages)) {
      $this->is_valid = False;
    }
    return  $this->is_valid;
  } 
}
?>