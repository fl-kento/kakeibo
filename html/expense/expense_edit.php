<?php
session_start();
try {
  $db = new PDO('mysql:dbname=money_management;host=127.0.0.1;charset=utf8', 'root', '');
} catch (PDOException $e) {
  echo 'DB接続エラー:' . $e->getMessage();
  header('Location: ../top.php');
  exit();
}
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $user = $db->prepare('SELECT name FROM user WHERE id = :id');
  $user->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $user->execute();
  $user = $user->fetch(PDO::FETCH_ASSOC);
  $user_name = $user['name'];
  $now_content = $db->prepare('SELECT amount, type_no, date FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id AND expense_no = :no');
  $now_content->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $now_content->bindParam(':no', $_REQUEST['no'], PDO::PARAM_INT);
  $now_content->execute();
  $now_content = $now_content->fetch(PDO::FETCH_ASSOC);
  if (!empty($_POST['edit'])) {
    if (empty($_POST['kinds'])) {
      $error_message['kinds'] = '種類を選んでください';
    }
    if (empty($_POST['money'])) {
      $error_message['money'] = '金額を入力してください';
    } elseif (!preg_match(' /^[0-9]+$/', $_POST['money'])) {
      $error_message['int'] = "金額は半角数字で入力してください";
    } elseif (strlen($_POST['money']) > 6) {
      $error_message['big'] = "金額が大きすぎます";
    }
    if (empty($_POST['date'])) {
      $error_message['date'] = '日付を選んでください';
    }
    if (empty($error_message)) {
      $edit = $db->prepare('UPDATE expense SET type_no = :type_no, amount = :money, date = :date WHERE user_id = :id AND expense_no = :expense_no');
      $edit->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
      $edit->bindParam(':expense_no', $_REQUEST['no'], PDO::PARAM_INT);
      $edit->bindParam(':type_no', $_POST['kinds'], PDO::PARAM_INT);
      $edit->bindParam(':money', $_POST['money'], PDO::PARAM_INT);
      $edit->bindParam(':date', $_POST['date'], PDO::PARAM_STR);
      $edit->execute();
      header('Location: expense.php');
      exit();
    }
  } else {
    $kind = $now_content['type_no'];
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
  <link rel="stylesheet" href="../../css/edit.css">
  <title>支出編集画面</title>
</head>
<body>
<div class="main">
    <div class="sidebar">
      <div class="sidebar_content"><span class="username"><?php echo $user_name; ?></span>さん</div>
      <div class="sidebar_content expense"><a href="expense.php">支出管理</a></div>
      <div class="sidebar_content"><a href="../income/income.php">収入管理</a></div>
      <div class="sidebar_content"><a href="../fc/fc.php">固定費管理</a></div>
      <div class="sidebar_content logout">
        <a href="../../logout.php">ログアウト</a>
      </div>
    </div>
    <div class="content">
      <h1 class="title">支出項目の編集・削除</h1>
      <form action="expense_edit.php?no=<?php print($_REQUEST['no']); ?>" method="post">
        <p>種類: <select name="kinds" class="item">
          <option value="1" <?php if ($kind == "1") {echo "selected";} ?>>食費</option>
          <option value="2" <?php if ($kind == "2") {echo "selected";} ?>>日用品</option>
          <option value="3" <?php if ($kind == "3") {echo "selected";} ?>>趣味</option>
          <option value="4" <?php if ($kind == "4") {echo "selected";} ?>>交通</option>
          <option value="5" <?php if ($kind == "5") {echo "selected";} ?>>教育</option>
          <option value="6" <?php if ($kind == "6") {echo "selected";} ?>>医療費</option>
          <option value="7" <?php if ($kind == "7") {echo "selected";} ?>>被服、美容</option>
          <option value="8" <?php if ($kind == "8") {echo "selected";} ?>>交際費</option>
          <option value="9" <?php if ($kind == "9") {echo "selected";} ?>>雑費</option>
        </select></p>
        <p class="item">金額: <input type="text" name="money" value="<?php echo $money; ?>"> 円</p>
        <p class="item">日付: <input type="date" name="date" value="<?php echo $date; ?>"></p>
        <p class="decision">
          <input type="submit" name="edit" value="編集">
          <input type="submit" name="delete" value="削除">
        </p>
      </form>
    </div>
  </div>
</body>
</html>