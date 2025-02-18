<?php
require_once('../Database.php');
require_once('../Validator.php');
class ExpenseManager {
  private $db;
  public function __construct() {
    $this->db = new Database();
  }
  public function getExpense($month, $year) {
    $sql = 'SELECT SUM(amount) AS 集計金額, name, type_no FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id AND month(date) = :month AND year(date) = :year GROUP BY type_no';    
    $params = [':id' => $_SESSION['id'], ':month' => $month, ':year' => $year];
    $summarize_amount = $this->db->query($sql, $params);
    $sql = 'SELECT SUM(amount) AS 合計金額 FROM expense WHERE user_id = :id AND month(date) = :month AND year(date) = :year';
    $params = [':id' => $_SESSION['id'], ':month' => $month, ':year' => $year];
    $total_amount = $this->db->fetch($sql, $params);
    $sql = 'SELECT user_id, expense_no, name, amount FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id 
      AND expense_no = (SELECT expense_no FROM expense WHERE user_id = :id ORDER BY expense_no DESC LIMIT 1)';
    $params = [':id' => $_SESSION['id']];
    $latest_expense = $this->db->fetch($sql, $params);
    return [$summarize_amount, $total_amount, $latest_expense];
  }
  public function getExpenseDetail($month, $year) {   
    $sql = 'SELECT expense_no, amount, date, content FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id AND type_no = :no AND month(date) = :month AND year(date) = :year ORDER BY date DESC';
    $params = [':id' => $_SESSION['id'], ':no' => $_REQUEST['no'], ':month' => $month, ':year' => $year];
    $expense_detail = $this->db->query($sql, $params);
    $sql = 'SELECT name FROM expense INNER JOIN type ON expense.type_no = type.id WHERE type_no = :no';
    $params = [':no' => $_REQUEST['no']];
    $title = $this->db->fetch($sql, $params);
    return [$expense_detail, $title];
  }
  public function getSumDailyAmount($date) {  
    $sql = 'SELECT SUM(amount) AS 合計金額 FROM expense WHERE user_id = :id AND type_no = :no AND date = :date GROUP BY date';
    $params = [':id' => $_SESSION['id'], ':no' => $_REQUEST['no'], ':date' => $date];
    $sum_amount = $this->db->fetch($sql, $params);
    return $sum_amount;
  }
  public function getNowContent() {
    $sql = 'SELECT amount, type_no, date, content FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id AND expense_no = :no';
    $params = [':id' => $_SESSION['id'], ':no' => $_REQUEST['no']];
    $now_content = $this->db->fetch($sql, $params);
    return $now_content;
  }
  public function addExpense() {
    $sql = 'SELECT IFNULL(MAX(expense_no), 0) + 1 AS latest_no FROM expense WHERE user_id = :id';
    $params = [':id' => $_SESSION['id']];
    $latest_no = $this->db->fetch($sql, $params);
    $validator = new Validator();
    $validator->checkContent($_POST['kinds'], $_POST['money'], $_POST['date']);
    if (empty($validator->error_message)) {
      $sql = 'INSERT INTO expense (expense_no, user_id, type_no, amount, date, content) VALUE (:number, :id, :type, :money, :date, :content)';
      $params = [':number' => $latest_no['latest_no'], ':id' => $_SESSION['id'], ':type' => $_POST['kinds'], ':money' => $_POST['money'], ':date' => $_POST['date'], ':content' => $_POST['content']];
      $this->db->execute($sql, $params);
      return 0;
    } else {
      return $validator->error_message;
    }
  }
  public function deleteExpense() {
    $sql = 'DELETE FROM expense WHERE user_id = :id AND expense_no = :no';
    $params = [':id' => $_SESSION['id'], ':no' => $_REQUEST['no']];
    $this->db->execute($sql, $params);
  }
  public function editExpense() {
    $validator = new Validator();
    $validator->checkContent($_POST['kinds'], $_POST['money'], $_POST['date'], $_POST['content']);
    if (empty($validator->error_message)) {
      $sql = 'UPDATE expense SET type_no = :type_no, amount = :money, date = :date, content = :content WHERE user_id = :id AND expense_no = :expense_no';
      $params = [':id' => $_SESSION['id'], ':expense_no' => $_REQUEST['no'], ':type_no' => $_POST['kinds'], ':money' => $_POST['money'], ':date' => $_POST['date'], ':content' => $_POST['content']];
      $this->db->execute($sql, $params);
      return 0;
    } else {
      return $validator->error_message;
    }
  }
}
?>