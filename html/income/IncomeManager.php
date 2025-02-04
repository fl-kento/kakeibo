<?php
require_once('../Validator.php');
require_once('../Database.php');
class IncomeManager {
  private $db;
  public function __construct() {
    $this->db = new Database();
  }
  public function getIncome($month, $year) {
    $sql = 'SELECT content, amount, income_no FROM income WHERE user_id = :id AND month(date) = :month AND year(date) = :year';
    $params = [':id' => $_SESSION['id'], ':month' => $month, ':year' => $year];
    $content = $this->db->query($sql, $params);
    $sql = 'SELECT SUM(amount) AS 合計金額 FROM income WHERE user_id = :id AND month(date) = :month AND year(date) = :year';
    $params = [':id' => $_SESSION['id'], ':month' => $month, ':year' => $year];
    $total_amount = $this->db->fetch($sql, $params);
    return [$content, $total_amount];
  }
  public function addIncome() {
    $sql = 'SELECT IFNULL(MAX(income_no), 0) + 1 AS latest_no FROM income WHERE user_id = :id';
    $params = [':id' => $_SESSION['id']];
    $latest_no = $this->db->fetch($sql, $params);
    $validator = new Validator();
    $validator->checkContent($_POST['content'], $_POST['money'], $_POST['date']);
    if (empty($validator->error_message)) {
      $sql = 'INSERT INTO income (income_no, user_id, content, amount, date) VALUE (:number, :id, :content, :money, :date)';
      $params = [':number' => $latest_no['latest_no'], ':id' => $_SESSION['id'], ':content' => $_POST['content'], ':money' => $_POST['money'], ':date' => $_POST['date']];
      $this->db->execute($sql, $params);
      return 0;
    } else {
      return $validator->error_message;
    }
  }
  public function getNowContent() {
    $sql = 'SELECT content, amount, date FROM income WHERE user_id = :id AND income_no = :no';
    $params = [':id' => $_SESSION['id'], ':no' => $_REQUEST['no']];
    $now_content = $this->db->fetch($sql, $params);
    return $now_content;
  }
  public function editIncome() {
    $validator = new Validator();
    $validator->checkContent($_POST['content'], $_POST['money'], $_POST['date']);
    if (empty($validator->error_message)) {
      $sql = 'UPDATE income SET content = :content, amount = :money, date = :date WHERE user_id = :id AND income_no = :no';
      $params = [':content' => $_POST['content'], ':money' => $_POST['money'], ':date' => $_POST['date'], ':id' => $_SESSION['id'], ':no' => $_REQUEST['no']];
      $this->db->execute($sql, $params);
      return 0;
    } else {
      return $validator->error_message;
    }
  }
}
?>