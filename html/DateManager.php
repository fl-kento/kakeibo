<?php
require_once('AbstractValidator.php');
require_once('DateValidator.php');
class DateManager {
  public function displayDate($month, $year) {
    $date_validator = new DateValidator();
    if ($date_validator->validate($month, $year)){
      $month = $_POST['month'];
      $year = $_POST['year'];
    } else {
      $error_message = $date_validator->getErrorMessages();
      $month = date('m');
      $year = date('Y');
    }
    return [$month, $year, $error_message];
  }
}
?>