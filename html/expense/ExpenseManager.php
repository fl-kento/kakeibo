<?php
require_once('../Database.php');
require_once('../Validator.php');
require_once('../ContentNumberSelector.php');
class ExpenseManager {
  public function displayExpense($month, $year) {
    $db = new Database();
    $sql = 'SELECT SUM(amount) AS 集計金額, name, type_no FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id AND month(date) = :month AND year(date) = :year GROUP BY type_no';    $params = [':id' => $_SESSION['id'], ':month' => $month, ':year' => $year];
    $summarize_amount = $db->query($sql, $params);
    $sql = 'SELECT SUM(amount) AS 合計金額 FROM expense WHERE user_id = :id AND month(date) = :month AND year(date) = :year';
    $params = [':id' => $_SESSION['id'], ':month' => $month, ':year' => $year];
    $total_amount = $db->fetch($sql, $params);
    $sql = 'SELECT user_id, expense_no, name, amount FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id 
      AND expense_no = (SELECT expense_no FROM expense WHERE user_id = :id ORDER BY expense_no DESC LIMIT 1)';
    $params = [':id' => $_SESSION['id']];
    $latest_expense = $db->fetch($sql, $params);
    return [$summarize_amount, $total_amount, $latest_expense];
  }
  public function displayExpenseDetail($month, $year) {
    $db = new Database();
    $sql = 'SELECT expense_no, amount, date FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id AND type_no = :no AND month(date) = :month AND year(date) = :year ORDER BY date DESC';
    $params = [':id' => $_SESSION['id'], ':no' => $_REQUEST['no'], ':month' => $month, ':year' => $year];
    $expense_detail = $db->query($sql, $params);
    $sql = 'SELECT name FROM expense INNER JOIN type ON expense.type_no = type.id WHERE type_no = :no';
    $params = [':no' => $_REQUEST['no']];
    $title = $db->fetch($sql, $params);
    return [$expense_detail, $title];
  }
  public function displayNowContent() {
    $db = new Database();
    $sql = 'SELECT amount, type_no, date FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id AND expense_no = :no';
    $params = [':id' => $_SESSION['id'], ':no' => $_REQUEST['no']];
    $now_content = $db->fetch($sql, $params);
    return $now_content;
  }
  public function addExpense() {
    $db = new Database();
    $content_number_selector = new ContentNumberSelector();
    $latest_no = $content_number_selector->selectContentNumber($_SESSION['id']);
    $validator = new Validator();
    $validator->checkContent($_POST['kinds'], $_POST['money'], $_POST['date']);
    if (empty($validator->error_message)) {
      $sql = 'INSERT INTO expense (expense_no, user_id, type_no, amount, date) VALUE (:number, :id, :kinds, :money, :date)';
      $params = [':number' => $latest_no['expense_no'], ':id' => $_SESSION['id'], ':kinds' => $_POST['kinds'], ':money' => $_POST['money'], ':date' => $_POST['date']];
      $db->execute($sql, $params);
      return 0;
    } else {
      return $validator->error_message;
    }
  }
  public function deleteExpense() {
    $db = new Database();
    $sql = 'DELETE FROM expense WHERE user_id = :id AND expense_no = :no';
    $params = [':id' => $_SESSION['id'], ':no' => $_REQUEST['no']];
    echo $_REQUEST['no'];
    $db->execute($sql, $params);
  }
  public function editExpense() {
    $db = new Database();
    $validator = new Validator();
    $validator->checkContent($_POST['kinds'], $_POST['money'], $_POST['date']);
    if (empty($validator->error_message)) {
      $sql = 'UPDATE expense SET type_no = :type_no, amount = :money, date = :date WHERE user_id = :id AND expense_no = :expense_no';
      $params = [':id' => $_SESSION['id'], ':expense_no' => $_REQUEST['no'], ':type_no' => $_POST['kinds'], ':money' => $_POST['money'], ':date' => $_POST['date']];
      $db->execute($sql, $params);
      return 0;
    } else {
      return $validator->error_message;
    }
  }
}
?>