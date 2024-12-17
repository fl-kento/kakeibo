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
  $latest_no = $db->prepare('SELECT fixed_no FROM fixed WHERE user_id = :id ORDER BY fixed_no DESC LIMIT 1');
  $latest_no->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $latest_no->execute();
  $latest_no = $latest_no->fetch(PDO::FETCH_ASSOC);
  if (empty($latest_no['fixed_no'])) {
    $latest_no['fixed_no'] = 1;
  } else {
    $latest_no['fixed_no'] += 1;
  }
  $error_message = [];
  if (!empty($_POST['add'])) {
    if (empty($_POST['content'])) {
      $error_message['content'] = '内容を入力してください';
    }
    if (empty($_POST['money'])) {
      $error_message['money'] = '金額を入力してください';
    }
    if (empty($_POST['payment_date'])) {
      $error_message['payment_date'] = '支払日を入力してください';
    } elseif (1 > $_POST['payment_date'] or $_POST['payment_date'] > 31) {
      $error_message['Incorrect_format'] = "正しい日にちを入力してください";
    }
    if (strlen($_POST['money']) > 6) {
      $error_message['big'] = "金額が大きすぎます";
    }
    if (mb_strlen($_POST['content']) > 20) {
      $error_message['length'] = "内容が長すぎます";
    }
    if (empty($error_message)) {
      $add = $db->prepare('INSERT INTO fixed (fixed_no, user_id, content, amount, payment_date) VALUE (:number, :id, :content, :money, :payment_date)');
      $add->bindParam(':number', $latest_no['fixed_no'], PDO::PARAM_INT);
      $add->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
      $add->bindParam(':content', $_POST['content'], PDO::PARAM_STR);
      $add->bindParam(':money', $_POST['money'], PDO::PARAM_INT);    
      $add->bindParam(':payment_date', $_POST['payment_date'], PDO::PARAM_INT);    
      $add->execute();
      header('Location: fc.php');
      exit();
    }
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
  <link rel="stylesheet" href="../../css/add.css">
  <title>固定費追加画面</title>
</head>
<body>
  <div class="main">
    <div class="sidebar">
      <div class="sidebar_content"><span class="username"><?php echo $user_name; ?></span>さん</div>
      <div class="sidebar_content"><a href="../expense/expense.php">支出管理</a></div>
      <div class="sidebar_content"><a href="../income/income.php">収入管理</a></div>
      <div class="sidebar_content fixedcosts"><a href="fc.php">固定費管理</a></div>
      <div class="sidebar_content logout">
        <a href="../../logout.php">ログアウト</a>
      </div>
    </div>
    <div class="content">
      <h1 class="title">固定費の追加</h1>
      <form action="fc_add.php" method="post">
      <h3><?php
        foreach ($error_message as $message) {
          echo $message . '<br>';
        } 
      ?></h3>
        <p>内容: <input type="text" name="content"></p>
        <p>金額: <input type="text" name="money"> 円</p>
        <p>支払日: <input type="number" name="payment_date" value="<?php echo date('j'); ?>"></p>
        <p class="decision"><input type="submit" name="add" value="追加"></p>
      </form>
    </div>
  </div>
</body>
</html>