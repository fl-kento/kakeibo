<?php
require_once('../UserManager.php');
require_once('ExpenseManager.php');
session_start();
$kind = '';
$text_value_amount = '';
$text_value_date = date("Y-m-d");
$error_message = [];
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $user_manager = new UserManager();
  $user_name = $user_manager->getName($_SESSION['id']);
  $expense_manager = new ExpenseManager();
  if (!empty($_POST['add'])) {
    $error_message = $expense_manager->addExpense();
    if (empty($error_message)) {
      header('Location: expense.php');
      exit();
    } else {
      $kind = $_POST['kinds'];
      $text_value_amount = $_POST['money'];
      $text_value_date = $_POST['date'];
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
  <title>支出追加画面</title>
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
      <h1 class="title">支払い内容の追加</h1>
      <form action="expense_add.php" method="post">
        <h3><?php
          foreach ($error_message as $message) {
            echo $message . '<br>';
          } 
        ?></h3>
        <p>種類: <select name="kinds">
          <option value="">種類を選んでください</option>
          <option value="1" <?php if ($kind == "1") {echo "selected";} ?>>食費</option>
          <option value="2" <?php if ($kind == "2") {echo "selected";} ?>>日用品</option>
          <option value="3" <?php if ($kind == "3") {echo "selected";} ?>>趣味</option>
          <option value="4" <?php if ($kind == "4") {echo "selected";} ?>>交通</option>
          <option value="5" <?php if ($kind == "5") {echo "selected";} ?>>教育</option>
          <option value="6" <?php if ($kind == "6") {echo "selected";} ?>>医療費</option>
          <option value="7" <?php if ($kind == "7") {echo "selected";} ?>>被服、美容</option>
          <option value="8" <?php if ($kind == "8") {echo "selected";} ?>>交際費</option>
          <option value="9" <?php if ($kind == "9") {echo "selected";} ?>>雑費</option>
          <option value="10" <?php if ($kind == "10") {echo "selected";} ?>>カスタム</option>
        </select></p>
        <p>金額: <input type="text" name="money" value = "<?php echo $text_value_amount; ?>"> 円</p>
        <p>日付: <input type="date" name="date" value="<?php echo $text_value_date; ?>"></p>
        <p class="decision"><input type="submit" name="add" value="追加"></p>
      </form>
    </div>
  </div>
</body>
</html>