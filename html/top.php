<?php
$error_message=[];
if(!empty($_POST["login"])){
  if(empty($_POST["name"])){
    $error_message["name"]="ユーザ名を入力してください";
  }elseif(mb_strlen($_POST["name"])>20){
    $error_message["name"]="文字数がオーバーしています";
  }
  if(empty($_POST["password"])){
    $error_message["password"]="パスワードを入力してください";
  }elseif(!preg_match(" /^[a-zA-Z0-9]+$/",$_POST["password"])){
    $error_message["password"]="大文字、小文字、数値で入力してください";
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
      <p class="error">
      <?php
        if(!empty($error_message["name"])){
            echo $error_message["name"];
          }
      ?>
      </p>
      <p>パスワード: <input type="password" name="password"></p>
      <p class="error">
      <?php
        if(!empty($error_message["password"])){
            echo $error_message["password"];
          }
      ?>
      </p>
      <input type="submit" name="login" value="ログイン">
    </form>
    <div class="button"><a href="register.html" class="register">新規登録</a></div>
  </div>
</body>
</html>