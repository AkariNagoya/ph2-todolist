<?php
require_once 'dbconnect.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: ./admin/auth/login.php');
    exit;
}

$stmt = $dbh->prepare('SELECT email FROM users WHERE id = :id');
$stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$email = $stmt->fetchColumn();

try {
    $stmt = $dbh->prepare('SELECT * FROM todos WHERE user_id = :user_id');
    $stmt->bindValue('user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'データ取得エラー: ' . htmlspecialchars($e->getMessage());
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ph2todolist</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">


<header class="w-full bg-blue-200 h-10 flex items-center justify-between px-4">
  <span class="text-sm">
    <?= htmlspecialchars($email) ?> のTo-Do List
  </span>

  <a
    href="./admin/auth/logout.php"
    class="text-sm hover:underline"
  >
    ログアウト
  </a>
</header>


  <div class="flex justify-center py-16">
  <div class="w-full max-w-xl text-center">
    <!-- ここに今のフォーム & ToDo一覧 -->

    <!-- 入力フォーム -->
    <form action="./admin/create/index.php" method="POST" class="mb-6">
      <input
        type="text"
        name="text"
        placeholder="新しいToDoを入力してください"
        class="w-full border rounded-md px-3 py-2 mb-3"
      >
      <button
        type="submit"
        class="w-32 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-md"
      >
        追加
      </button>
    </form>
      
      <!-- ToDo一覧 -->
    <div class="space-y-3">
      <?php foreach ($todos as $todo): ?>
        <div class="flex items-center justify-center gap-3">
          <span class="w-24 text-left">
            <?= htmlspecialchars($todo['text']) ?>
          </span>

          <form action="./admin/update/index.php" method="POST">
            <input type="hidden" name="id" value="<?= $todo['id']?>">
            <button
            type="submit"
            class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded"
            >
            <?= $todo['complete'] ? 'Undo' : 'Complete' ?>
            </button>
          </form>
          <!-- <a
            href="./admin/update/index.php?id=<?= $todo['id'] ?>"
            class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded"
            >
            Complete
          </a> -->
          
          <a
          href="./admin/edit/index.php?id=<?= $todo['id'] ?>"
          class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm px-3 py-1 rounded"
          >
          Edit
        </a>
        
          <form action="./admin/delete/index.php" method="POST">
            <input type="hidden" name="id" value="<?= $todo['id'] ?>">
            <button
            type="submit"
            class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded"
            >
            Delete
            </button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  </div>

</body>
</html>