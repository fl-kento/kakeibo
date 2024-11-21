<?php
$errorMessage = [];
if (!empty($_POST['login'])) {
  if (empty($_POST['name'])) {
    $errorMessage['name'] = 'ユーザ名を入力してください';
  }
  if (empty($_POST['password'])) {
    $errorMessage['password'] = 'パスワードを入力してください';
  }
  if (empty($errorMessage)) {
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
   <title>Document</title>
</head>
<body>
  <div class="wrapper">
    <h1 class="title">家計簿アプリ</h1>
    <form action="top.php" method="post">
      <p>ユーザー名: <input type="text" name="name"></p>
      <p class="error"><?php
        if (!empty($errorMessage['name'])) {
          echo $errorMessage['name'];
        } 
      ?></p>
      <p>パスワード: <input type="password" name="password"></p>
      <p class="error"><?php
        if (!empty($errorMessage['password'])) {
          echo $errorMessage['password'];
        }
      ?></p>
      <input type="submit" name="login" value="ログイン">
    </form>
    <div class="button"><a href="register.html" class="register">新規登録</a></div>
  </div>
</body>
</html>