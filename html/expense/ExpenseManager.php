<?php
require_once('../Database.php');
class ExpenseManager {
  public function displayExpense($month, $year) {
    $db = new Database();
    $sql = 'SELECT SUM(amount) AS 集計金額, name, type_no FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id AND month(date) = :month AND year(date) = :year GROUP BY type_no';
    $params = [':id' => $_SESSION['id'], ':month' => $month, ':year' => $year];
    $summarize_amount = $db->query($sql, $params);
    $sql = 'SELECT name FROM user WHERE id = :id';
    $params = [':id' => $_SESSION['id']];
    $user = $db->fetch($sql, $params);
    $user_name = $user['name'];
    $sql = 'SELECT SUM(amount) AS 合計金額 FROM expense WHERE user_id = :id AND month(date) = :month AND year(date) = :year';
    $params = [':id' => $_SESSION['id'], ':month' => $month, ':year' => $year];
    $total_amount = $db->fetch($sql, $params);
    $sql = 'SELECT user_id, expense_no, name, amount FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id 
      AND expense_no = (SELECT expense_no FROM expense WHERE user_id = :id ORDER BY expense_no DESC LIMIT 1)';
    $params = [':id' => $_SESSION['id']];
    $latest_expense = $db->fetch($sql, $params);
    return [$summarize_amount, $user_name, $total_amount, $latest_expense];
  }
}
?>