<?php
session_start();
$flag = True;
try {
  $db = new PDO('mysql:dbname=money_management;host=127.0.0.1;charset=utf8', 'root', '');
} catch (PDOException $e) {
  echo 'DB接続エラー:' . $e->getMessage();
  $flag = False;
}
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time() && $flag) {
  $_SESSION['time'] = time();
  $user = $db->prepare('SELECT name FROM user WHERE id = :id');
  $user->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $user->execute();
  $user = $user->fetch(PDO::FETCH_ASSOC);
  $user_name = $user['name'];
  $latest_no = $db->prepare('SELECT income_no FROM income WHERE user_id = :id ORDER BY income_no DESC LIMIT 1');
  $latest_no->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $latest_no->execute();
  $latest_no = $latest_no->fetch(PDO::FETCH_ASSOC);
  if (empty($latest_no['income_no'])) {
    $latest_no['income_no'] = 1;
  } else {
    $latest_no['income_no'] += 1;
  }
  $error_message = [];
  if (!empty($_POST['add'])) {
    if (empty($_POST['content'])) {
      $error_message['content'] = '内容を入力してください';
    } elseif (mb_strlen($_POST['content']) > 20) {
      $error_message['length'] = "内容が長すぎます";
    }
    if (empty($_POST['money'])) {
      $error_message['money'] = '金額を入力してください';
    } elseif (strlen($_POST['money']) > 6) {
      $error_message['big'] = "金額が大きすぎます";
    } elseif (!preg_match(' /^[0-9]+$/', $_POST['money'])) {
      $error_message['int'] = "金額は半角数字で入力してください";
    }
    if (empty($_POST['date'])) {
      $error_message['date'] = '日付を選んでください';
    }
    if (empty($error_message)) {
      $add = $db->prepare('INSERT INTO income (income_no, user_id, content, amount, date) VALUE (:number, :id, :content, :money, :date)');
      $add->bindParam(':number', $latest_no['income_no'], PDO::PARAM_INT);
      $add->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
      $add->bindParam(':content', $_POST['content'], PDO::PARAM_STR);
      $add->bindParam(':money', $_POST['money'], PDO::PARAM_INT);    
      $add->bindParam(':date', $_POST['date'], PDO::PARAM_STR);    
      $add->execute();
      header('Location: income.php');
      exit();
    }
  }
} else {
  header('Location: ../top.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="keywords" content="家計簿アプリ,使いやすい,household account book">
  <meta name="description" content="家計簿アプリです">
  <link rel="stylesheet" href="../../css/main.css">
  <link rel="stylesheet" href="../../css/add.css">
  <title>収入追加画面</title>
</head>
<body>
  <div class="main">
    <div class="sidebar">
      <div class="sidebar_content"><span class="username"><?php echo $user_name; ?></span>さん</div>
      <div class="sidebar_content"><a href="../expense/expense.php">支出管理</a></div>
      <div class="sidebar_content income"><a href="income.php">収入管理</a></div>
      <div class="sidebar_content"><a href="../fc/fc.php">固定費管理</a></div>
      <div class="sidebar_content">
        <a href="../../logout.php">ログアウト</a>
      </div>
    </div>
    <div class="content">
      <h1 class="title">収入の追加</h1>
      <form action="income_add.php" method="post">
        <h3><?php
          foreach ($error_message as $message) {
            echo $message . '<br>';
          } 
        ?></h3>
        <p>内容: <input type="text" name="content"></p>
        <p>金額: <input type="text" name="money"> 円</p>
        <p>日付: <input type="date" name="date" value="<?php echo date("Y-m-d"); ?>"></p>
        <p class="decision"><input type="submit" name="add" value="追加"></p>
      </form>
    </div>
  </div>
</body>
</html>