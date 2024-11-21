<?php 
session_start();
$_SESSION['id'] = '1'; //実際にはログイン時に取得
$_SESSION['time'] = time(); //実際にはログイン時に取得
try {
  $db = new PDO('mysql:dbname=money_management;host=127.0.0.1;charset=utf8', 'root', '');
}
catch (PDOException $e) {
  echo 'DB接続エラー:' . $e->getMessage();
}
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $content = $db->prepare('SELECT SUM(amount) AS 集計金額, name FROM expense JOIN type ON expense.type_no = type.id WHERE user_id = :id GROUP BY type_no');
  $content->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $content->execute();
  $user = $db->prepare('SELECT name FROM user WHERE id = :id');
  $user->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $user->execute();
  $userName = $user->fetch(PDO::FETCH_ASSOC);
  $sum = $db->prepare('SELECT SUM(amount) AS 合計金額 FROM expense WHERE user_id = :id');
  $sum->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $sum->execute();
  $sumResult = $sum->fetch(PDO::FETCH_ASSOC);
  $sql = 'SELECT user_id, expense_no, name, amount FROM expense JOIN type ON expense.type_no = type.id WHERE user_id = :id 
    AND expense_no = (SELECT expense_no FROM expense WHERE user_id = :id ORDER BY expense_no DESC LIMIT 1)';
  $latestExpense = $db->prepare($sql);
  $latestExpense->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $latestExpense->execute();
  $latestExpense = $latestExpense->fetch(PDO::FETCH_ASSOC);
}
else {
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
  <link rel="stylesheet" href="../../css/expense/expense.css">
  <title>Document</title>
</head>
<body>
  <div class="main">
    <div class="sidebar">
      <div class="sidebar_content"><span class="username"><?php echo $userName['name']; ?></span>さん</div>
      <div class="sidebar_content expense"><a href="expense.php">支出管理</a></div>
      <div class="sidebar_content income"><a href="../income/income.php">収入管理</a></div>
      <div class="sidebar_content fixedcosts"><a href="../fc/fc.php">固定費管理</a></div>
      <div class="sidebar_content logout">
        <a href="../../logout.php">ログアウト</a>
      </div>
    </div>
    <div class="content">
      <h1>10月の支出</h1>
      <a class="btn_add" href="expense_add.php">支出の追加</a>
      <p class=sum>合計:<?php
        if (empty($sumResult['合計金額'])) {
          echo '0';
        }
        else {
          echo $sumResult['合計金額']; 
        }
      ?>円</p>
      <div class="table_box">
        <table>
        <?php 
        foreach($content->fetchAll() as $result): 
        ?>
          <tr>
            <td><?php echo $result['name']; ?></td>
            <td><?php echo $result['集計金額']; ?></td>
            <td><a href="expense_check.php">編集・削除</a></td>          
          </tr>
        <?php endforeach; ?> 
        </table>
      </div>
      <p class="latest_expense">直近の支出 <?php
        if (empty($latestExpense['name'])) {
          echo 'なし';
        }
        else {
          echo $latestExpense['name'] . ' : ' . $latestExpense['amount'] . '円'; 
        }
      ?></p> 
    </div>
  </div>
</body>
</html>