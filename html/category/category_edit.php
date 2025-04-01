<?php
require_once('../Database.php');

require_once('../UserManager.php');
require_once('CategoryManager.php');
session_start();
$user_manager = new UserManager();
$user_manager->checkLogin();
$user_name = $user_manager->getName($_SESSION['id']);
$error_message = [];
$category_manager = new CategoryManager();
$types = $category_manager->getCategory();
if (!empty($_POST['add'])) {
  $error_message = $category_manager->addCategory();
  if (empty($error_message)) {
    header('Location: category_edit.php');
    exit();
  }
}
if (!empty($_POST['delete'])) {
  if (!empty($_POST['id'])) {
    $ids = $_POST['id'];
    $placeholders = [];
    $params = [];
    foreach ($ids as $index => $id) {
      $placeholder = ':id' . $index;
      $placeholders[] = $placeholder;
      $params[$placeholder] = (int)$id;
    }
    $sql = "DELETE FROM type WHERE id IN (" . implode(',', $placeholders) . ")";
    try {
      $db = new Database();
      $db->execute($sql, $params);
      header('Location: category_edit.php');
      exit();
    } catch (PDOException $e) {
      $error_message["type"] = "支出などに登録されているカテゴリーは削除できません。";
    }
  } else {
    $error_message["type"] = "削除する項目が選択されていません。";
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
  <link rel="stylesheet" href="../../css/category.css">
  <title>支出追加画面</title>
</head>
<body>
  <div class="main">
    <div class="sidebar">
      <div class="sidebar_content"><span class="username"><?php echo $user_name; ?></span>さん</div>
      <div class="sidebar_content"><a href="../expense/expense.php">支出管理</a></div>
      <div class="sidebar_content"><a href="../income/income.php">収入管理</a></div>
      <div class="sidebar_content"><a href="../fc/fc.php">固定費管理</a></div>
      <div class="sidebar_content category"><a href="category_edit.php">カテゴリー管理</a></div>
      <div class="sidebar_content logout">
        <a href="../../logout.php">ログアウト</a>
      </div>
    </div>
    <div class="content">
      <h1 class="title">カテゴリー管理</h1>
      <h3 class="error_message"><?php
        foreach ($error_message as $message) {
          echo $message . '<br>';
        } 
      ?></h3>
      <div class="show_category">
        <h3>カテゴリー一覧</h3>
        <form action="category_edit.php" method="post">
          <table>
            <?php foreach ($types as $type) : ?>
              <tr>
                <td></td>
                <td><input type="checkbox" id="<?php echo $type['id'] ?>" name="id[]" value="<?php echo $type['id'] ?>"></td>
                <td><label for="<?php echo $type['id'] ?>"><?php echo $type['name']; ?></label></td>
              </tr>
            <?php endforeach; ?>
          </table>
          <p>選択した項目を : <input type="submit" name="delete" value="削除"></p>
        </form>
      </div>
      <div class="add_category">
        <h3>カテゴリーの追加</h3>
        <form action="category_edit.php" method="post">
          <p>カテゴリー名: <input type="text" name="name"></p>
          <p class="decision"><input type="submit" name="add" value="追加"></p>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
