<?php
require_once('../Validator.php');
require_once('../Database.php');
class FcManager {
  private $db;
  public function __construct() {
    $this->db = new Database();
  }
  public function getFixContent() {
    $sql = 'SELECT content, amount, payment_date, fixed_no FROM fixed WHERE user_id = :id';
    $params = [':id' => $_SESSION['id']];
    $content = $this->db->query($sql, $params);
    $sql = 'SELECT SUM(amount) AS 合計金額 FROM fixed WHERE user_id = :id';
    $params = [':id' => $_SESSION['id']];
    $total_amount = $this->db->fetch($sql, $params);
    return [$content, $total_amount];
  }
  public function addFixContent() {
    $sql = 'SELECT IFNULL(MAX(fixed_no), 0) + 1 AS latest_no FROM fixed WHERE user_id = :id';
    $params = [':id' => $_SESSION['id']];
    $latest_no = $this->db->fetch($sql, $params);
    $validator = new Validator();
    $validator->checkFixContent($_POST['content'], $_POST['money'], $_POST['payment_date']);
    if (empty($validator->error_message)) {
      $sql = 'INSERT INTO fixed (fixed_no, user_id, content, amount, payment_date) VALUE (:number, :id, :content, :money, :payment_date)';
      $params = [':number' => $latest_no['latest_no'], ':id' => $_SESSION['id'], ':content' => $_POST['content'], ':money' => $_POST['money'], ':payment_date' => $_POST['payment_date']];
      $this->db->execute($sql, $params);
      return 0;
    } else {
      return $validator->error_message;
    }
  }
  public function getNowContent() {
    $sql = 'SELECT content, amount, payment_date FROM fixed WHERE user_id = :id AND fixed_no = :no';
    $params = [':id' => $_SESSION['id'], ':no' => $_REQUEST['no']];
    $now_content = $this->db->fetch($sql, $params);
    return $now_content;
  }
  public function editFixContent() {
    $validator = new Validator();
    $validator->checkFixContent($_POST['content'], $_POST['money'], $_POST['date']);
    if (empty($validator->error_message)) {
      $sql = 'UPDATE fixed SET content = :content, amount = :money, payment_date = :date WHERE user_id = :id AND fixed_no = :no';
      $params = [':content' => $_POST['content'], ':money' => $_POST['money'], ':date' => $_POST['date'], ':id' => $_SESSION['id'], ':no' => $_REQUEST['no']];
      $this->db->execute($sql, $params);
      return 0;
    } else {
      return $validator->error_message;
    }
  }
}
?>