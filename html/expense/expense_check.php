<?php
require_once('../UserManager.php');
require_once('ExpenseManager.php');
session_start();
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $user_manager = new UserManager();
  $user_name = $user_manager->displayUser($_SESSION['id']);
  $expense_manager = new ExpenseManager();
  list($content, $title) = $expense_manager->displayExpenseDetail($_REQUEST['month'], $_REQUEST['year']);
  if (!empty($_POST['delete'])) {
    $expense_manager->deleteExpense();
    header('Location: expense.php');
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
        foreach ($content as $result): 
        ?>
          <tr>
            <td><?php echo $result['date']; ?></td>
            <td><?php echo number_format($result['amount']); ?></td>
            <td><a href="expense_edit.php?no=<?php print($result['expense_no']); ?>">編集</a></td> 
            <td><form action="expense_check.php?no=<?php print($result['expense_no']); ?>" method="post"><input type="submit" value = "削除" name = "delete"></form></td>         
          </tr>
        <?php endforeach; ?> 
        </table>
      </div>
    </div>
  </div>
</body>
</html>