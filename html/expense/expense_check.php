<?php
require_once('../UserManager.php');
require_once('ExpenseManager.php');
session_start();
$user_manager = new UserManager();
$user_manager->checkLogin();
$user_name = $user_manager->getName($_SESSION['id']);
$expense_manager = new ExpenseManager();
list($contents, $title) = $expense_manager->getExpenseDetail($_REQUEST['month'], $_REQUEST['year']);
if (!empty($_POST['delete'])) {
  $expense_manager->deleteExpense();
  header('Location: expense.php');
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
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
    <div class="item_list">
      <?php $date = ''; ?>
      <div class="item">    
        <?php foreach ($contents as $value): ?>
        <?php if ($date != date('n月j日', strtotime($value['date']))): ?>
        <?php 
          $date = date('n月j日', strtotime($value['date'])); 
          $sum_amount = $expense_manager->getSumDailyAmount($value['date']);
        ?>
        <div class="date_amount">
          <div class="date"><?php echo $date; ?></div>
          <div class="amount">合計: ¥<?php echo number_format($sum_amount['合計金額']); ?></div>
        </div>
        <?php endif; ?>
        <div class="item_detail">
          <div class="main_content">
            <div class="matter"><?php echo $value['content']; ?></div>
            <div class="money">¥<?php echo number_format($value['amount']); ?></div>
          </div>
          <div class="select">
            <a href="expense_edit.php?no=<?php echo $value['expense_no']; ?>"><span class="material-icons">edit</span></a>
            <form action="expense_check.php?no=<?php echo $value['expense_no']; ?>" method="post">
              <input type="submit" name="delete" value="削除">
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div> 
</body>
</html>