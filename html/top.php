<?php
session_start();
$error_message = [];
try {
  $db = new PDO('mysql:dbname=money_management;host=127.0.0.1;charset=utf8', 'root', '');
} catch (PDOException $e) {
  $error_message['db'] = "データベースに接続できていません"; 
}
if (!empty($_POST['login'])) {
  if (empty($_POST['name'])) {
    $error_message['name'] = 'ユーザ名を入力してください';
  }
  if (empty($_POST['password'])) {
    $error_message['password'] = 'パスワードを入力してください';
  }
  if (empty($error_message)) {
    $login = $db->prepare('SELECT id, name, password FROM user WHERE name = :user_name AND password = :pass');
    $login->bindParam(':user_name', $_POST['name'], PDO::PARAM_INT);
    $login->bindParam(':pass', $_POST['password'], PDO::PARAM_STR);
    $login->execute();
    $member = $login->fetch();
    if ($member) {
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();
      header('Location: expense/expense.php');
      exit();
    } else {
      $error_message['login'] = 'ユーザ名かパスワードが異なっています';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="keywords" content="家計簿アプリ,使いやすい,household account book">
  <meta name="description" content="家計簿アプリです">
  <link rel="stylesheet" href="../css/top.css">
   <title>ログイン画面</title>
</head>
<body>
  <div class="wrapper">
    <h1 class="title">家計簿アプリ</h1>
    <p class="error"><?php
      if (!empty($error_message['login'])) {
        echo $error_message['login'];
      } 
      if (!empty($error_message['db'])) {
        echo $error_message['db'];
      } 
    ?></p>
    <form action="top.php" method="post">
      <p>ユーザー名: <input type="text" name="name"></p>
      <p class="error"><?php
        if (!empty($error_message['name'])) {
          echo $error_message['name'];
        } 
      ?></p>
      <p>パスワード: <input type="password" name="password"></p>
      <p class="error"><?php
        if (!empty($error_message['password'])) {
          echo $error_message['password'];
        }
      ?></p>
      <input type="submit" name="login" value="ログイン">
    </form>
    <div class="button"><a href="register.php" class="register">新規登録</a></div>
  </div>
</body>
</html>