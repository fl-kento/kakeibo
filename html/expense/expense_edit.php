<?php
require_once('ExpenseManager.php');
require_once('../UserManager.php');
session_start();
$error_message = [];
$user_manager = new UserManager();
$user_manager->checkLogin();
$user_name = $user_manager->getName($_SESSION['id']);
$expense_manager = new ExpenseManager();
$now_content = $expense_manager->getNowContent();
$kind = $now_content['type_no'];
$money = $now_content['amount'];
$date = $now_content['date'];
$content = $now_content['content'];
if (!empty($_POST['edit'])) {
  $error_message = $expense_manager->editExpense();
  if (empty($error_message)) {
    header('Location: expense.php');
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
  <link rel="stylesheet" href="../../css/main.css">
  <link rel="stylesheet" href="../../css/edit.css">
  <title>支出編集画面</title>
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
      <h1 class="title">支出項目の編集</h1>
      <form action="expense_edit.php?no=<?php print($_REQUEST['no']); ?>" method="post">
        <h3><?php
          foreach ($error_message as $message) {
            echo $message . '<br>';
          } 
        ?></h3>
        <p>カテゴリー: <select name="kinds">
          <option value="">カテゴリーを選んでください</option>
          <?php
          $expense_type = $expense_manager->getType();
          foreach ($expense_type as $type) {
            echo '<option value="' . $type['id'] . '"';
            if ($kind == $type['id']) {
              echo 'selected';
            }
            echo '>' . $type['name'] . '</option>';
          }
          ?>
        </select></p>
        <p class="item">金額: <input type="text" name="money" value="<?php echo $money; ?>"> 円</p>
        <p class="item">日付: <input type="date" name="date" value="<?php echo $date; ?>"></p>
        <p class="item">内容: <input type="text" name="content" value="<?php echo $content; ?>"></p>
        <p class="decision">
          <input type="submit" name="edit" value="更新">
        </p>
        <a class="back" href="expense_check.php?no=<?php echo $now_content['type_no']; ?>&month=<?php echo date('n', strtotime($date)); ?>&year=<?php echo date('Y', strtotime($date)); ?>">戻る</a>
      </form>     
    </div>
  </div>
</body>
</html>