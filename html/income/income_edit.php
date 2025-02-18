<?php
require_once('incomeManager.php');
require_once('../UserManager.php');
session_start();
$user_manager = new UserManager();
$user_manager->checkLogin();
$user_name = $user_manager->getName($_SESSION['id']);
$income_manager = new IncomeManager();
$now_content = $income_manager->getNowContent();
$content = $now_content['content'];
$money = $now_content['amount'];
$date = $now_content['date'];
$error_message = [];
if (!empty($_POST['edit'])) {
  $error_message = $income_manager->editIncome();
  if (empty($error_message)) {
    header('Location: income.php');
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
  <title>収入編集画面</title>
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
      <h1 class="title">収入項目の編集・削除</h1>
      <form action="income_edit.php?no=<?php print($_REQUEST['no']); ?>" method="post">
        <h3><?php
          foreach ($error_message as $message) {
            echo $message . '<br>';
          } 
        ?></h3>
        <p class="item">内容: <input type="text" name="content" value="<?php echo $content; ?>"></p>
        <p class="item">金額: <input type="text" name="money" value="<?php echo $money; ?>"> 円</p>
        <p class="item">日付: <input type="date" name="date" value="<?php echo $date; ?>"></p>
        <p class="decision">
          <input type="submit" name="edit" value="編集">
          <input type="submit" name="delete" value="削除">
        </p>
      </form>
    </div>
  </div>
</body>
</html>