<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="keywords" content="家計簿アプリ,使いやすい,household account book">
  <meta name="description" content="家計簿アプリです">
  <link rel="stylesheet" href="../../css/main.css">
  <link rel="stylesheet" href="../../css/income/income_add.css">
  <title>Document</title>
</head>
<body>
  <div class="main">
    <div class="sidebar">
      <div class="username"><span>研人</span>さん</div>
      <div class="sidebar_content payment"><a href="../expence/expense.html">支出管理</a></div>
      <div class="sidebar_content income"><a href="income.html">収入管理</a></div>
      <div class="sidebar_content fixedcosts"><a href="../fc/fc.html">固定費管理</a></div>
      <div class="sidebar_content logout"><a href="../top.html">ログアウト</a></div>
    </div>
    <div class="content">
      <h1 class="title">収入の追加</h1>
      <form action="income.html" method="post">
        <p>内容: <input type="text" name="name"></p>
        <p>金額: <input type="text" name="money"> 円</p>
        <p>日付: <input type="date" name="date"></p>
        <p class="decision"><input type="submit" name="add" value="追加"></p>
      </form>
    </div>
  </div>
</body>
</html>