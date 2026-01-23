<?php
require_once __DIR__ . '/dbconnect.php';
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
      <input
        type="text"
        name="text"
        placeholder="新しいToDoを入力してください"
        class="w-full border rounded-md px-3 py-2 mb-3"
        id="todo-text"
      >
      <button
        type="button"
        class="w-32 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-md"
        id="js-create-todo"
      >
        追加
      </button>
      
      <!-- ToDo一覧 -->
    <div class="space-y-3" id="js-todo-list">
      <?php foreach ($todos as $todo): ?>
        <div class="flex items-center justify-center gap-3" data-id="<?= $todo['id'] ?>">
          <span class="w-24 text-left">
            <?= htmlspecialchars($todo['text']) ?>
          </span>

            <button
            type="button"
            id="js-complete-todo"
            data-id="<?= $todo['id'] ?>"
            class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded"
            >
            <?= $todo['complete'] ? 'Undo' : 'Complete' ?>
            </button>
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
        
            <button
            type="button"
            class="js-delete-todo bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded"
            >
            Delete
            </button>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  </div>
  <template id="js-template">
  <div id="js-todo-template" class="flex items-center justify-center gap-3">
    <span id="js-todo-text" class="w-24 text-left"></span>
    <!-- js-complete-todo クラスはステータスの更新処理で使うため、ここでクラスに追記しています -->
    <button 
    type="button"
    id="js-complete-todo-template"
    class="js-complete-todo bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded"
    data-id="">
      Complete
    </button>
    <a href="" id="js-edit-todo-template" class="bg-yellow-400 hover:bg-yellow-500 text-white text-sm px-3 py-1 rounded">
      Edit
    </a>
    <button
    type="button"
    id="js-delete-todo-template"
    class="js-delete-todo bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded"
    data-id="">
      Delete
    </button>
  </div>
  </template>
<script src="./script.js"></script>
</body>
</html>