<?php
require_once '../../dbconnect.php';
$id = $_GET['id'] ?? '';
$text = $_GET['text'] ?? '';

// To-Doを取得
try{
  $stmt = $dbh->prepare('SELECT * FROM todos WHERE id = :id');
  $stmt->bindValue(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $todos = $stmt->fetch(PDO::FETCH_ASSOC);
  $text = $todos['text'] ?? '';
}catch(PDOException $e){
  echo 'データ取得エラー'. htmlspecialchars($e->getMessage());
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id']??'';
  $text = trim($_POST['text'] ?? '');
  if (empty($id)) {
    header('Location: ../../index.php');
    exit;
  }

  try{
    $stmt = $dbh->prepare('UPDATE todos SET text = :text WHERE id = :id');
    $stmt->bindValue(':text', $text, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
  }catch(PDOException $e){
  echo '更新エラー'. htmlspecialchars($e->getMessage());
  exit;
  }
  header("Location: ../../index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ToDo編集</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white">

<div class="max-w-md mx-auto mt-20 text-center">

  <form action="./index.php" method="POST">

    <!-- id は hidden -->
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

    <!-- 入力欄（初期値あり） -->
    <input
      type="text"
      name="text"
      value="<?= htmlspecialchars($text) ?>"
      class="w-full border px-3 py-2 rounded mb-4"
      required
    >

    <!-- 更新ボタン -->
    <button
      type="submit"
      class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-8 py-2 rounded"
    >
      更新
    </button>

  </form>

</div>

</body>
</html>