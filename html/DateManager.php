<?php
require_once('Validator.php');
class DateManager {
  public function displayDate($month, $year) {
    $validator = new Validator();
    $validator->checkDate($month, $year);
    $error_message = $validator->error_message;
    if (empty($error_message)) {
      $month = $_POST['month'];
      $year = $_POST['year'];
    } else {
      $month = date('m');
      $year = date('Y');
    }
    return [$month, $year, $error_message];
  }
}