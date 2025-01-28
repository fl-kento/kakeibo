<?php
require_once('Database.php');
require_once('Validator.php');
class DateManager {
  public function displayDate($month, $year) {
    $date = new Validator();
    $date->checkDate($month, $year, 'date');
    $error_message = $date->error_message;
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