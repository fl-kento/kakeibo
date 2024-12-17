<?php 
session_start();
$flag = True;
try {
  $db = new PDO('mysql:dbname=money_management;host=127.0.0.1;charset=utf8', 'root', '');
} catch (PDOException $e) {
  echo 'DB接続エラー:' . $e->getMessage();
  $flag = False;
}
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time() && $flag) {
  $_SESSION['time'] = time();
  $user = $db->prepare('SELECT name FROM user WHERE id = :id');
  $user->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $user->execute();
  $user = $user->fetch(PDO::FETCH_ASSOC);
  $user_name = $user['name'];
  $now_content = $db->prepare('SELECT content, amount, payment_date FROM fixed WHERE user_id = :id AND fixed_no = :no');
  $now_content->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $now_content->bindParam(':no', $_REQUEST['no'], PDO::PARAM_INT);
  $now_content->execute();
  $now_content = $now_content->fetch(PDO::FETCH_ASSOC);
  $error_message = [];
  $content = $now_content['content'];
  $money = $now_content['amount'];
  $date = $now_content['payment_date'];
  if (!empty($_POST['edit'])) {
    if (empty($_POST['content'])) {
      $error_message['content'] = '内容を入力してください';
    }
    if (empty($_POST['money'])) {
      $error_message['money'] = '金額を入力してください';
    } elseif (strlen($_POST['money']) > 6) {
      $error_message['big'] = "金額が大きすぎます";
    } elseif (!preg_match(' /^[0-9]+$/', $_POST['money'])) {
      $error_message['int'] = "金額は半角数字で入力してください";
    } 
    if (empty($_POST['date'])) {
      $error_message['payment_date'] = '支払日を入力してください';
    } elseif (1 > $_POST['date'] or $_POST['date'] > 31) {
      $error_message['Incorrect_format'] = "正しい日にちを入力してください";
    }
    if (mb_strlen($_POST['content']) > 20) {
      $error_message['length'] = "内容が長すぎます";
    }
    if (empty($error_message)) {
      $edit = $db->prepare('UPDATE fixed SET content = :content, amount = :money, payment_date = :date WHERE user_id = :id AND fixed_no = :no');
      $edit->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
      $edit->bindParam(':no', $_REQUEST['no'], PDO::PARAM_INT);
      $edit->bindParam(':content', $_POST['content'], PDO::PARAM_STR);
      $edit->bindParam(':money', $_POST['money'], PDO::PARAM_INT);
      $edit->bindParam(':date', $_POST['date'], PDO::PARAM_STR);
      $edit->execute();
      header('Location: fc.php');
      exit();
    }
  }
  if (!empty($_POST['delete'])) {
    $delete = $db->prepare('DELETE FROM fixed WHERE user_id = :id AND fixed_no = :no');
    $delete->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
    $delete->bindParam(':no', $_REQUEST['no'], PDO::PARAM_INT);
    $delete->execute();
    header('Location: fc.php');
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
  <title>固定費編集画面</title>
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
      <h1 class="title">固定費項目の編集・削除</h1>
      <form action="fc_edit.php?no=<?php print($_REQUEST['no']); ?>" method="post">
        <h3><?php
          foreach ($error_message as $message) {
            echo $message . '<br>';
          } 
        ?></h3>
        <p class="item">内容: <input type="text" name="content" value="<?php echo $content; ?>"></p>
        <p class="item">金額: <input type="text" name="money" value="<?php echo $money; ?>"> 円</p>
        <p class="item">支払日: <input type="number" name="date" value="<?php echo $date; ?>"> 日</p>
        <p class="decision">
          <input type="submit" name="edit" value="編集">
          <input type="submit" name="delete" value="削除">
        </p>
      </form>
    </div>
  </div>
</body>
</html>