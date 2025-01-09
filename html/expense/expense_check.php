<?php
session_start();
try {
  $db = new PDO('mysql:dbname=money_management;host=127.0.0.1;charset=utf8', 'root', '');
} catch (PDOException $e) {
  echo 'DB接続エラー:' . $e->getMessage();
  header('Location: ../top.php');
  exit();
}
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $user = $db->prepare('SELECT name FROM user WHERE id = :id');
  $user->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $user->execute();
  $user = $user->fetch(PDO::FETCH_ASSOC);
  $user_name = $user['name'];
  $content = $db->prepare('SELECT amount, date, expense_no FROM expense INNER JOIN type ON expense.type_no = type.id WHERE user_id = :id AND type_no = :no AND month(date) = :month AND year(date) = :year ORDER BY date DESC');
  $content->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $content->bindParam(':no', $_REQUEST['no'], PDO::PARAM_INT);
  $content->bindParam(':month', $_REQUEST['month'], PDO::PARAM_INT);
  $content->bindParam(':year', $_REQUEST['year'], PDO::PARAM_INT);
  $content->execute();
  $title = $db->prepare('SELECT name FROM expense INNER JOIN type ON expense.type_no = type.id WHERE type_no = :no');
  $title->bindParam(':no', $_REQUEST['no'], PDO::PARAM_INT);
  $title->execute();
  $title = $title->fetch(PDO::FETCH_ASSOC);
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
  <link rel="stylesheet" href="../../css/expense_check.css">
  <title>支出詳細画面</title>
</head>
<body>
<div class="main">
    <div class="sidebar">
      <div class="sidebar_content"><span class="username"><?php echo $user_name; ?></span>さん</div>
      <div class="sidebar_content expense"><a href="expense.php">支出管理</a></div>
      <div class="sidebar_content"><a href="../income/income.php">収入管理</a></div>
      <div class="sidebar_content"><a href="../fc/fc.php">固定費管理</a></div>
      <div class="sidebar_content logout">
        <a href="../../logout.php">ログアウト</a>
      </div>
    </div>
    <div class="content">
      <h1><?php echo $title['name']; ?> 履歴</h1>
      <div class="table_box">
        <table>
        <?php 
        foreach ($content->fetchAll() as $result): 
        ?>
          <tr>
            <td><?php echo $result['date']; ?></td>
            <td><?php echo $result['amount']; ?></td>
            <td><a href="expense_edit.php?no=<?php print($result['expense_no']); ?>">編集・削除</a></td>          
          </tr>
        <?php endforeach; ?> 
        </table>
      </div>
    </div>
  </div>
</body>
</html>