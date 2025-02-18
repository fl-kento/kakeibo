<?php
require_once('UserManager.php');
session_start();
$error_message = [];
$user_manager = new UserManager();
$text_value_name = '';
$text_value_password = '';
if (!empty($_POST['login'])) {
  $result = $user_manager->loginUser($_POST);
  if (is_array($result)) {
    $error_message = $result;
    $text_value_name = $_POST['name'];
    $text_value_password = $_POST['password'];
  } else {
    $_SESSION['id'] = $result;
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
      <p>ユーザー名: <input type="text" name="name" value="<?php echo $text_value_name ?>"></p>
      <p class="error"><?php
        if (!empty($error_message['name'])) {
          echo $error_message['name'];
        } 
      ?></p>
      <p>パスワード: <input type="password" name="password" value="<?php echo $text_value_password ?>"></p>
      <p class="error"><?php
        if (!empty($error_message['password'])) {
          echo $error_message['password'];
        }
      ?></p>
      <input type="submit" name="login" value="ログイン">
    </form>
    <div class="button"><a href="registration.php" class="register">新規登録</a></div>
  </div>
</body>
</html>