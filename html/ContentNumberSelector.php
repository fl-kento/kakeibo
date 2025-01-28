<?php
require_once('Database.php');
class ContentNumberSelector {
  public function selectContentNumber($user_id) {
    $db = new Database();
    $sql = 'SELECT expense_no FROM expense WHERE user_id = :id ORDER BY expense_no DESC LIMIT 1';
    $params = [':id' => $user_id];
    $latest_no = $db->fetch($sql, $params);
    if (empty($latest_no['expense_no'])) {
      $latest_no['expense_no'] = 1;
    } else {
      $latest_no['expense_no'] += 1;
    }
    return $latest_no;
  }
}
?>