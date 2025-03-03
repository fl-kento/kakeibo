やるべきこと
■html
・aタグの範囲を全体に→aをブロック要素に
・編集・削除画面で削除できていい気がする（支出）
・単体編集・削除画面から一覧画面に戻るボタン追加
・ログアウトの位置を下げる
・入力可能な桁数をあらかじめ表示する（内容、新規登録問わず）
→どっちがどっちか
・編集・削除画面の「編集ボタン」→「更新ボタン」
■php
・追加、変更、新規登録など、入力していた内容がエラーではじかれた場合、もともと入力していたものを残しておく
・固定費の支払日1~31までしか入力できないように←要検討
・年数はさすがに一年前後にする
→追加、確認両方とも
・収入管理→編集→ブラウザの戻るでエラーにならないように
・同じユーザ名、パスワードが通ってしまう→ユーザ名を一意に
→phpのほうで管理。dbは変えない
済　・いちまん 
・エラーメッセージの具体性を上げる（何がどんな感じでアウトか）
・数値の項目の入力、全体的な入力の改善
・htmlの入力に対応しないようにする

■新機能
・支出、固定費、収入を自由記述→カテゴリをチェックボックス
・カテゴリはカスタム欄を作る
・グラフ追加
・支出や収入のコメント機能→検討

■auto loadいったんしない

<?php
$host = "localhost";
$dbname = "kakeibo";
$username = "root";
$password = "";
$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

// SQLクエリでカテゴリごとの合計金額を取得
$sql = "SELECT kind, SUM(amount) as total FROM expense GROUP BY kind";
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$jsonData = json_encode($data);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>家計簿 円グラフ</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        canvas {
            max-width: 500px;
            margin: auto;
        }
    </style>
</head>
<body>

    <canvas id="expenseChart"></canvas>
    <script>
        // PHP から JSON データを取得
        const data = <?php echo $jsonData; ?>;
        
        const labels = data.map(item => item.kind);
        const amounts = data.map(item => item.total);

        function getColorForCategory(kind) {
            let hash = 0;
            for (let i = 0; i < kind.length; i++) {
                hash = kind.charCodeAt(i) + ((hash << 5) - hash);
            }
            const hue = Math.abs(hash) % 360; // 0〜360 の範囲に収める
            return `hsl(${hue}, 70%, 60%)`; // 明るめの色を生成
        }

        const backgroundColor = labels.map(kind => getColorForCategory(kind));

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
</body>
</html>
