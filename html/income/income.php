<?php
require_once('IncomeManager.php');
require_once('../UserManager.php');
require_once('../DateManager.php');
session_start();
$user_manager = new UserManager();
$user_manager->checkLogin();
$month = date('m');
$year = date('Y');
if (!empty($_POST['change'])) {
  $date_manager = new DateManager();
  list($month, $year, $error_message) = $date_manager->displayDate($_POST['month'], $_POST['year']);
}
$user_name = $user_manager->getName($_SESSION['id']);
$income_manager = new IncomeManager();
list($content, $total_amount) = $income_manager->getIncome($month, $year);
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
        if (empty($total_amount['合計金額'])) {
          echo '0';
        } else {
          echo number_format($total_amount['合計金額']); 
        }
      ?>円</p>
      <div class="table_box">
        <table>
        <?php 
        foreach ($content as $result): 
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