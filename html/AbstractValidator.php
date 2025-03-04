<?php
require_once('Database.php');
abstract class AbstractValidator
{
    protected $error_messages = [];
    protected $db;
    protected $is_valid = True;
    public function __construct() {
      $this->db = new Database();
    }
    abstract public function validate(array $post_data);
    public function getErrorMessages() {
        return $this->error_messages;
    }
}
?>