<?php
try {
  $db = new PDO('mysql:dbname=money_management;host=127.0.0.1;charset=utf8', 'root', '');
} catch (PDOException $e) {
  echo 'DB接続エラー:' . $e->getMessage();
}
$error_message = [];
$latest_no = $db->prepare('SELECT id FROM user ORDER BY id DESC LIMIT 1');
$latest_no->execute();
$latest_no = $latest_no->fetch(PDO::FETCH_ASSOC);
$latest_no['id'] += 1;
if (!empty($_POST['login'])) {
  if (empty($_POST['name'])) {
    $error_message['name'] = 'ユーザ名を入力してください';
  }
  elseif (mb_strlen($_POST['name']) > 20) {
    $error_message['name'] = '文字数がオーバーしています';
  }
  if (empty($_POST['password'])) {
    $error_message['password'] = 'パスワードを入力してください';
  }
  else {
    if (!preg_match(' /^[a-zA-Z0-9]+$/', $_POST['password'])) {
      $error_message['password'] = '大文字、小文字、数値で入力してください';
    }
    if (mb_strlen($_POST['password']) < 10) {
      $error_message['password'] = '10桁以上で入力してください';
    }
  }
  if (empty($error_message)) {
    $regist = $db->prepare('INSERT INTO user (id, name, password) VALUE (:id, :name, :password)');
    $regist->bindParam(':id', $latest_no['id'], PDO::PARAM_INT);
    $regist->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
    $regist->bindParam(':password', $_POST['password'], PDO::PARAM_STR);
    $regist->execute();
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
      <p class="decision"><input type="submit" name="login" value="新規登録"></p>
    </form>
    <div class="button"><a href="top.php" class="register">トップ画面に戻る</a></div>
  </div>
</body>
</html>