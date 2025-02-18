<?php
require_once('FcManager.php');
require_once('../UserManager.php');
session_start();
$user_manager = new UserManager();
$user_manager->checkLogin();
$user_name = $user_manager->getName($_SESSION['id']);
$fix_manager = new FcManager();
list($content, $total_amount) = $fix_manager->getFixContent($_SESSION['id']);
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
        if (empty($total_amount['合計金額'])) {
          echo '0';
        } else {
          echo number_format($total_amount['合計金額']); 
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
          <?php foreach ($content as $result): ?>
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