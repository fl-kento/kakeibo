<?php
require_once('../UserManager.php');
require_once('ExpenseManager.php');
session_start();
$kind = '';
$text_value_amount = '';
$text_value_content = '';
$text_value_date = date("Y-m-d");
$error_message = [];
$user_manager = new UserManager();
$user_manager->checkLogin();
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
    $text_value_content = $_POST['content'];
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
        <p>金額: <input type="text" name="money" value = "<?php echo $text_value_amount; ?>"> 円</p>
        <p>日付: <input type="date" name="date" value="<?php echo $text_value_date; ?>"></p>
        <p>内容: <input type="text" name="content" value="<?php echo $text_value_content; ?>"></p>
        <p class="decision"><input type="submit" name="add" value="追加"></p>
      </form>
    </div>
  </div>
</body>
</html>