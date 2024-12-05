<?php
session_start();
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
  $now_content = $db->prepare('SELECT content, amount, date FROM income WHERE user_id = :id AND income_no = :no');
  $now_content->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $now_content->bindParam(':no', $_REQUEST['no'], PDO::PARAM_INT);
  $now_content->execute();
  $now_content = $now_content->fetch(PDO::FETCH_ASSOC);
  $error_message = [];
  $content = $now_content['content'];
  $money = $now_content['amount'];
  $date = $now_content['date'];
  if (!empty($_POST['edit'])) {
    if (empty($_POST['content'])) {
      $error_message['content'] = '内容を入力してください';
    }
    if (empty($_POST['money'])) {
      $error_message['money'] = '金額を入力してください';
    }
    if (empty($_POST['date'])) {
      $error_message['date'] = '日付を選んでください';
    }
    if (strlen($_POST['money']) > 6) {
      $error_message['big'] = "金額が大きすぎます";
    }
    if (mb_strlen($_POST['content']) > 16) {
      $error_message['length'] = "内容が長すぎます";
    }
    if (empty($error_message)) {
      $edit = $db->prepare('UPDATE income SET content = :content, amount = :money, date = :date WHERE user_id = :id AND income_no = :no');
      $edit->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
      $edit->bindParam(':no', $_REQUEST['no'], PDO::PARAM_INT);
      $edit->bindParam(':content', $_POST['content'], PDO::PARAM_STR);
      $edit->bindParam(':money', $_POST['money'], PDO::PARAM_INT);
      $edit->bindParam(':date', $_POST['date'], PDO::PARAM_STR);
      $edit->execute();
      header('Location: income.php');
      exit();
    }
  } else {

  }
  if (!empty($_POST['delete'])) {
    $delete = $db->prepare('DELETE FROM income WHERE user_id = :id AND income_no = :no');
    $delete->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
    $delete->bindParam(':no', $_REQUEST['no'], PDO::PARAM_INT);
    $delete->execute();
    header('Location: income.php');
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
  <title>収入編集画面</title>
</head>
<body>
  <div class="main">
  <div class="sidebar">
      <div class="sidebar_content"><span class="username"><?php echo $user_name; ?></span>さん</div>
      <div class="sidebar_content"><a href="../expense/expense.php">支出管理</a></div>
      <div class="sidebar_content income"><a href="income.php">収入管理</a></div>
      <div class="sidebar_content"><a href="../fc/fc.php">固定費管理</a></div>
      <div class="sidebar_content logout">
        <a href="../../logout.php">ログアウト</a>
      </div>
    </div>
    <div class="content">
      <h1 class="title">収入項目の編集・削除</h1>
      <form action="income_edit.php?no=<?php print($_REQUEST['no']); ?>" method="post">
        <h3><?php
          foreach ($error_message as $message) {
            echo $message . '<br>';
          } 
        ?></h3>
        <p class="item">内容: <input type="text" name="content" value="<?php echo $content; ?>"></p>
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