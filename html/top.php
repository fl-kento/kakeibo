<?php
require_once('CheckValidation.php');
session_start();
$error_message = [];
$login = new CheckValidation();
if (!empty($_POST['login'])) {
  $login->check($_POST, 'login');
  if (!empty($login->error_message)) {
    $error_message = $login->error_message;
  } else {
    $_SESSION['id'] = $login->user_id;
    $_SESSION['time'] = time();
    header('Location: expense/expense.php');
    exit();
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