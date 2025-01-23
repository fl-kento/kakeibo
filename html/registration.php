<?php
require_once('UserManager.php');
$error_message = [];
$text_value_name = '';
$text_value_password = '';
$register = new UserManager();
if (!empty($_POST['register'])) {
  $result = $register->registUser($_POST);
  if ($result) {
    $error_message = $result;
    $text_value_name = $_POST['name'];
    $text_value_password = $_POST['password'];
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
    <form action="registration.php" method="post">
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
      <p class="decision"><input type="submit" name="register" value="新規登録"></p>
    </form>
    <div class="button"><a href="top.php" class="register">トップ画面に戻る</a></div>
  </div>
</body>
</html>