<?php
require_once('Database.php');
class Validator {
  public $error_message = [];
  public $user_id;
  public $db;
  public function __construct() {
    $this->db = new Database();
  }
  public function checkUser($name, $password, $type) {
    if ($type === 'login') {
      if (empty($name)) {
        $this->error_message['name'] = 'ユーザー名を入力してください';
      }
      if (empty($password)) {
        $this->error_message['password'] = 'パスワードを入力してください';
      }
      if (empty($this->error_message)) {
        $sql = 'SELECT id, name, password FROM user WHERE name = :user_name AND password = :pass';
        $params = [':user_name' => $name, ':pass' => $password];
        $result = $this->db->fetch($sql, $params);
        if ($result) {
          $this->user_id = $result['id'];
        } else {
          $this->error_message['login'] = 'ユーザー名かパスワードが異なっています';
        }
      }
    } elseif ($type === 'register') {
      if (empty($name)) {
        $this->error_message['name'] = 'ユーザー名を入力してください';
      } elseif (mb_strlen($name) > 20) {
        $this->error_message['name'] = '20文字以下で入力してください';
      } else {
        $sql = 'SELECT name FROM user WHERE name = :user_name';
        $params = [':user_name' => $name];
        $result = $this->db->fetch($sql, $params);
        if ($result) {
          $this->error_message['name'] = 'ユーザー名が既に使用されています';
        }
      }
      if (empty($password)) {
        $this->error_message['password'] = 'パスワードを入力してください';
      } else {
        if (!preg_match(' /^[a-zA-Z0-9]+$/', $password)) {
          $this->error_message['password'] = '大文字、小文字、数値で入力してください';
        }
        if (mb_strlen($password) < 10) {
          $this->error_message['password'] = '10桁以上で入力してください';
        }
      }
    }
  }
  public function checkDate($month, $year) {
    if (empty($month) || empty($year)) {
      $this->error_message = '数値を入力してください';
    }
    elseif (0 > $month || $month > 12) {
      $this->error_message = '正しい形式で入力してください';
    }
    elseif (2026 < $year || $year < 2024) {
      $this->error_message = '2024~2026年で入力してください';
    }
  }
  public function checkContent($kind, $money, $date) {
    if (empty($kind)) {
      $this->error_message['kinds'] = '種類を選んでください';
    }
    if (empty($money)) {
      $this->error_message['money'] = '金額を入力してください';
    } elseif (!preg_match(' /^[0-9]+$/', $money)) {
      $this->error_message['int'] = "金額は半角数字で入力してください";
    } elseif (strlen($money) > 6) {
      $this->error_message['big'] = "金額が大きすぎます";
    }
    if (empty($date)) {
      $this->error_message['date'] = '日付を選んでください';
     } elseif (2027 < $date || $date < 2024) {
      $this->error_message['date'] = '2024~2026年で入力してください';
    }
  }
  public function checkFixContent($content, $money, $payment_date) {
    if (empty($content)) {
      $this->error_message['content'] = '内容を入力してください';
    } elseif (mb_strlen($_POST['content']) > 20) {
      $error_message['length'] = "内容が長すぎます";
    }
    if (empty($money)) {
      $this->error_message['money'] = '金額を入力してください';
    } elseif (!preg_match(' /^[0-9]+$/', $money)) {
      $this->error_message['int'] = "金額は半角数字で入力してください";
    } elseif (strlen($money) > 6) {
      $this->error_message['big'] = "金額が大きすぎます";
    }
    if (empty($payment_date)) {
      $this->error_message['payment_date'] = '支払日を入力してください';
    } elseif (1 > $_POST['payment_date'] or $_POST['payment_date'] > 31) {
      $this->error_message['Incorrect_format'] = "正しい日にちを入力してください";
    }
  }
}
?>