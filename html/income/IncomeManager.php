<?php
require_once('../Database.php');
require_once('../AbstractValidator.php');
require_once('../FixContentValidator.php');
require_once('../ContentValidator.php');
class IncomeManager {
  private $db;
  public function __construct() {
    $this->db = new Database();
  }
  public function getType() {
    $sql = 'SELECT * FROM type';
    $expense_type = $this->db->query($sql);
    return $expense_type;
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
    $content_validator = new ContentValidator();
    if ($content_validator->validate($_POST)) {
      $sql = 'INSERT INTO income (income_no, user_id, type_no, content, amount, date) VALUE (:number, :id, :type, :content, :money, :date)';
      $params = [':number' => $latest_no['latest_no'], ':id' => $_SESSION['id'], ':type' => $_POST['kinds'], ':content' => $_POST['content'], ':money' => $_POST['money'], ':date' => $_POST['date']];
      $this->db->execute($sql, $params);
      return 0;
    } else {
      return $content_validator->getErrorMessages();
    }
  }
  public function getNowContent() {
    $sql = 'SELECT content, amount, date FROM income WHERE user_id = :id AND income_no = :no';
    $params = [':id' => $_SESSION['id'], ':no' => $_REQUEST['no']];
    $now_content = $this->db->fetch($sql, $params);
    return $now_content;
  }
  public function editIncome() {
    $content_validator = new ContentValidator();
    if ($content_validator->validate($_POST)) {
      $sql = 'UPDATE income SET content = :content, amount = :money, date = :date WHERE user_id = :id AND income_no = :no';
      $params = [':content' => $_POST['content'], ':money' => $_POST['money'], ':date' => $_POST['date'], ':id' => $_SESSION['id'], ':no' => $_REQUEST['no']];
      $this->db->execute($sql, $params);
      return 0;
    } else {
      return $content_validator->getErrorMessages();
    }
  }
}
?>