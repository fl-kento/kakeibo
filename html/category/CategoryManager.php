<?php
require_once('../Database.php');
require_once('../AbstractValidator.php');
require_once('../CategoryValidator.php');
class CategoryManager {
  private $db;
  public function __construct() {
    $this->db = new Database();
  }
  public function getCategory() {
    $sql = 'SELECT * FROM type WHERE user_id = :id';
    $params = [':id' => $_SESSION['id']];
    $category = $this->db->query($sql, $params);
    return $category;
  }
  public function addCategory() {
    $sql = 'SELECT IFNULL(MAX(id), 0) + 1 AS latest_no FROM type';
    $latest_no = $this->db->fetch($sql);
    $category_validator = new CategoryValidator();
    if ($category_validator->validate($_POST)) {
      $sql = 'INSERT INTO type (id, name, user_id) VALUE (:type, :name, :user_id)';
      $params = [':type' => $latest_no['latest_no'], ':name' => $_POST['name'], ':user_id' => $_SESSION['id']];
      $this->db->execute($sql, $params);
      return 0;
    } else {
      return $category_validator->getErrorMessages();
    }
  }
  public function deleteCategory() {
    
  }
}
?>