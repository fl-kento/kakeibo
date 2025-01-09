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
  if (!empty($_POST['change'])) {
    if (empty($_POST['month']) || empty($_POST['year'])) {
      $error_message = '数値を入力してください';
    }
    elseif (0 > $_POST['month'] || $_POST['month'] > 12) {
      $error_message = '正しい形式で入力してください';
    }
    if (empty($error_message)) {
      $month = $_POST['month'];
      $year = $_POST['year'];
    } else {
      $month = date('n');
      $year = date('Y');
    }
  } else {
    $month = date('n');
    $year = date('Y');
  }
  $content = $db->prepare('SELECT content, amount, income_no FROM income WHERE user_id = :id AND month(date) = :month AND year(date) = :year');
  $content->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $content->bindParam(':month', $month, PDO::PARAM_INT);
  $content->bindParam(':year', $year, PDO::PARAM_INT);
  $content->execute();
  $user = $db->prepare('SELECT name FROM user WHERE id = :id');
  $user->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $user->execute();
  $user = $user->fetch(PDO::FETCH_ASSOC);
  $user_name = $user['name'];
  $sum = $db->prepare('SELECT SUM(amount) AS 合計金額 FROM income WHERE user_id = :id AND month(date) = :month AND year(date) = :year');
  $sum->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $sum->bindParam(':month', $month, PDO::PARAM_INT);
  $sum->bindParam(':year', $year, PDO::PARAM_INT);
  $sum->execute();
  $sum_result = $sum->fetch(PDO::FETCH_ASSOC);
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
  <link rel="stylesheet" href="../../css/manage.css">
  <link rel="stylesheet" href="https://unpkg.com/modern-css-reset/dist/reset.min.css"/>
  <title>収入管理画面</title>
</head>
<body>
  <div class="main">
  <div class="sidebar">
      <div class="sidebar_content"><span class="username"><?php echo $user_name; ?></span>さん</div>
      <div class="sidebar_content"><a href="../expense/expense.php">支出管理</a></div>
      <div class="sidebar_content income"><a href="income.php">収入管理</a></div>
      <div class="sidebar_content"><a href="../fc/fc.php">固定費管理</a></div>
      <div class="sidebar_content logout">
        <a href="../../logout.php">ログアウト</a>
      </div>
    </div>
    <div class="content">
      <form action="income.php" method="post">
        <h1>
          <input type="number" name="year" class="select_year" value="<?php echo $year; ?>"a>
          <input type="number" name="month" class="select_month" value="<?php echo $month; ?>"a>月の収入
          <input type="submit" class="change_month" name="change" value="変更">
        </h1>
        <h3><?php
        if (!empty($error_message)) {
          echo ($error_message);
        }
        ?></h3>
      </form>
      <a class="btn_add" href="income_add.php">収入の追加</a>
      <p class="sum">合計:<?php
        if (empty($sum_result['合計金額'])) {
          echo '0';
        } else {
          echo number_format($sum_result['合計金額']); 
        }
      ?>円</p>
      <div class="table_box">
        <table>
        <?php 
        foreach ($content->fetchAll() as $result): 
        ?>
          <tr>
            <td><?php echo $result['content']; ?></td>
            <td><?php echo number_format($result['amount']); ?> 円</td>
            <td><a href="income_edit.php?no=<?php print($result['income_no']); ?>">編集・削除</a></td>          
          </tr>
        <?php endforeach; ?> 
        </table>
      </div>
    </div>
  </div>
</body>
</html>