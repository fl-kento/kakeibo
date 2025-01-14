<?php
require_once('CheckValidation.php');
session_start();
$error_message = [];
$register = new CheckValidation();
if (!empty($_POST['register'])) {
  $register->check($_POST, 'register');
  if (!empty($register->error_message)) {
    $error_message = $register->error_message;
  } else {
    header('Location: completed.php');
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
  <title>新規登録画面</title>
</head>
<body>
  <div class="wrapper">  
    <h1 class="title">新規登録</h1>
    <form action="register.php" method="post">
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
      <p class="decision"><input type="submit" name="register" value="新規登録"></p>
    </form>
    <div class="button"><a href="top.php" class="register">トップ画面に戻る</a></div>
  </div>
</body>
</html>