<?php
require_once('ExpenseManager.php');
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
$user_manager = new UserManager();
$user_name = $user_manager->getName($_SESSION['id']);
$expense_manager = new ExpenseManager();
list($summarize_amount, $total_amount, $latest_expense) = $expense_manager->getExpense($month, $year);
$data = $summarize_amount;
$jsonData = json_encode($data);
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
  <title>支出管理画面</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="main">
    <div class="sidebar">
      <div class="sidebar_content"><span class="username"><?php echo $user_name; ?></span>さん</div>
      <div class="sidebar_content expense"><a href="expense.php">支出管理</a></div>
      <div class="sidebar_content"><a href="../income/income.php">収入管理</a></div>
      <div class="sidebar_content"><a href="../fc/fc.php">固定費管理</a></div>
      <div class="sidebar_content"><a href="../category/category_edit.php">カテゴリー管理</a></div>
      <div class="sidebar_content logout">
        <a href="../../logout.php">ログアウト</a>
      </div>
    </div>
    <div class="content">
      <form action="expense.php" method="post">
        <h1>
          <input type="number" name="year" class="select_year" value="<?php echo $year; ?>"a>
          <input type="number" name="month" class="select_month" value="<?php echo $month; ?>"a>月の支出
          <input type="submit" class="change_month" name="change" value="変更">
        </h1>
        <h3><?php
        if (!empty($error_message)) {
          echo ($error_message);
        }
        ?></h3>
      </form>
      <a class="btn_add" href="expense_add.php">支出の追加</a>
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
        foreach($summarize_amount as $result): 
        ?>
          <tr>
            <td><?php echo $result['name']; ?></td>
            <td><?php echo number_format($result['集計金額']); ?></td>
            <td><a href="expense_check.php?no=<?php echo $result['type_no']; ?>&month=<?php echo $month; ?>&year=<?php echo $year; ?>">編集・削除</a></td>          
          </tr>
        <?php endforeach; ?> 
        </table>
      </div>

      <canvas id="expenseChart"></canvas>
      <script>
        // PHP から JSON データを取得
        const data = <?php echo $jsonData; ?>;
        
        const labels = data.map(item => item.name);
        const amounts = data.map(item => item.集計金額);

        function getColorForCategory(name) {
          let hash = 0;
          for (let i = 0; i < name.length; i++) {
            hash = name.charCodeAt(i) + ((hash << 5) - hash);
          }
          const hue = Math.abs(hash) % 360; // 0〜360 の範囲に収める
          return `hsl(${hue}, 70%, 60%)`; // 明るめの色を生成
        }
        const backgroundColor = labels.map(name => getColorForCategory(name));
        const ctx = document.getElementById('expenseChart').getContext('2d');
        new Chart(ctx, {
          type: 'pie',
          data: {
            labels: labels,
            datasets: [{
              data: amounts,
              backgroundColor: backgroundColor
            }]
          }
        });
      </script>

      <p class="latest_expense">直近の支出 <?php
        if (empty($latest_expense['name'])) {
          echo 'なし';
        } else {
          echo $latest_expense['name'] . ' : ' . number_format($latest_expense['amount']) . '円'; 
        }
      ?></p> 
    </div>
  </div>
</body>
</html>