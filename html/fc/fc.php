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
  $content = $db->prepare('SELECT content, amount, payment_date, fixed_no FROM fixed WHERE user_id = :id');
  $content->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $content->execute();
  $user = $db->prepare('SELECT name FROM user WHERE id = :id');
  $user->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
  $user->execute();
  $user = $user->fetch(PDO::FETCH_ASSOC);
  $user_name = $user['name'];
  $sum = $db->prepare('SELECT SUM(amount) AS 合計金額 FROM fixed WHERE user_id = :id');
  $sum->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
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
  <title>固定費管理画面</title>
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
      <h1 class="sum">合計金額:<?php
        if (empty($sum_result['合計金額'])) {
          echo '0';
        } else {
          echo number_format($sum_result['合計金額']); 
        }
      ?>円</h1>
      <a class="btn_add" href="fc_add.php">固定費の追加</a>
      <div class="table_box">
        <table>
          <tr>
            <th>内容</th>
            <th>金額</th>
            <th>支払日</th>
            <th>編集・削除</th>
          </tr>
          <?php foreach ($content->fetchAll() as $result): ?>
          <tr>
            <td><?php echo $result['content']; ?></td>
            <td><?php echo number_format($result['amount']); ?> 円</td>
            <td><?php echo $result['payment_date']; ?> 日</td>
            <td><a href="fc_edit.php?no=<?php print($result['fixed_no']); ?>">編集・削除</a></td>          
          </tr>
          <?php endforeach; ?>
          <?php if (empty($result)): ?>
          <tr>
            <td colspan="4">なし</td>
          </tr>
          <?php endif; ?>
        </table>
      </div>
    </div>
  </div>
</body>
</html>