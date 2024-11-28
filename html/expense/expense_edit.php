<?php
session_start();
$_SESSION['id'] = '1'; //実際にはログイン時に取得
$_SESSION['time'] = time(); //実際にはログイン時に取得
try {
  $db = new PDO('mysql:dbname=money_management;host=127.0.0.1;charset=utf8', 'root', '');
} catch (PDOException $e) {
  echo 'DB接続エラー:' . $e->getMessage();
}
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $user = $db->prepare('SELECT name FROM user WHERE id = :id');
  $user->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $user->execute();
  $user = $user->fetch(PDO::FETCH_ASSOC);
  $user_name = $user['name'];
  $now_content = $db->prepare('SELECT name, amount, type_no, date FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id AND expense_no = :no');
  $now_content->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $now_content->bindParam(':no', $_REQUEST['no'], PDO::PARAM_INT);
  $now_content->execute();
  $now_content = $now_content->fetch(PDO::FETCH_ASSOC);
  if (!empty($_POST['edit'])) {
    if ($_POST['kinds'] != '0') {
      $kind = $_POST['kinds'];
    } else {
      $kind = $now_content['type_no'];
    }
    if (!empty($_POST['money'])) {
      $money = $_POST['money'];
    } else {
      $money = $now_content['amount'];
    }
    if (!empty($_POST['date'])) {
      $date = $_POST['date'];
    } else {
      $date = $now_content['date'];
    }
    $edit = $db->prepare('UPDATE expense SET type_no = :type_no, amount = :money, date = :date WHERE user_id = :id AND expense_no = :expense_no');
    $edit->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
    $edit->bindParam(':expense_no', $_REQUEST['no'], PDO::PARAM_INT);
    $edit->bindParam(':type_no', $kind, PDO::PARAM_INT);
    $edit->bindParam(':money', $money, PDO::PARAM_INT);
    $edit->bindParam(':date', $date, PDO::PARAM_STR);
    $edit->execute();
    header('Location: expense.php');
    exit();
  } else {
    $kind = $now_content['name'];
    $money = $now_content['amount'];
    $date = $now_content['date'];
  }
  if (!empty($_POST['delete'])) {
    $delete = $db->prepare('DELETE FROM expense WHERE user_id = :id AND expense_no = :expense_no');
    $delete->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
    $delete->bindParam(':expense_no', $_REQUEST['no'], PDO::PARAM_INT);
    $delete->execute();
    header('Location: expense.php');
    exit();
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
  <link rel="stylesheet" href="../../css/expense/expense_edit.css">
  <title>支出編集画面</title>
</head>
<body>
<div class="main">
    <div class="sidebar">
      <div class="sidebar_content"><span class="username"><?php echo $user_name; ?></span>さん</div>
      <div class="sidebar_content expense"><a href="expense.php">支出管理</a></div>
      <div class="sidebar_content income"><a href="../income/income.php">収入管理</a></div>
      <div class="sidebar_content fixedcosts"><a href="../fc/fc.php">固定費管理</a></div>
      <div class="sidebar_content logout">
        <a href="../../logout.php">ログアウト</a>
      </div>
    </div>
    <div class="content">
      <h1 class="title">支出項目の編集・削除</h1>
      <form action="expense_edit.php?no=<?php print($_REQUEST['no']); ?>" method="post">
        <p>種類: <select name="kinds">
          <option value="0" selected>種類を選んでください</option>
          <option value="1">食費</option>
          <option value="2">日用品</option>
          <option value="3">趣味</option>
          <option value="4">交通</option>
          <option value="5">教育</option>
          <option value="6">医療費</option>
          <option value="7">被服、美容</option>
          <option value="8">交際費</option>
          <option value="9">雑費</option>
        </select></p>
        <p class="now_value">現在の種類:<?php echo $kind; ?></p>
        <p>金額: <input type="text" name="money"> 円</p>
        <p class="now_value">現在の金額:<?php echo $money; ?>円</p>
        <p>日付: <input type="date" name="date"></p>
        <p class="now_value">現在の日付:<?php echo $date; ?></p>
        <p class="decision">
          <input type="submit" name="edit" value="編集">
          <input type="submit" name="delete" value="削除">
        </p>
      </form>
    </div>
  </div>
</body>
</html>