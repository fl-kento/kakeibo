<?php
class DateValidator extends AbstractValidator {
  public function validate($month, $year) {
    if (empty($month) || empty($year)) {
      $this->error_message = '数値を入力してください';
    }
    elseif (0 > $month || $month > 12) {
      $this->error_message = '正しい形式で入力してください';
    }
    elseif (2026 < $year || $year < 2024) {
      $this->error_message = '2024~2026年で入力してください';
    }
    if (!empty($this->error_message)) {
      $this->is_valid = False;
    }
    return $this->is_valid;
  }
}
?>