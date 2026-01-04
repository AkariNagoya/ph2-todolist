<?php
require_once '../../dbconnect.php';
session_start();

// 既にログイン済みの場合はリダイレクト
if (isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit;
}

// ログイン処理
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // --- バリデーション ---
    if ($email === '' || $password === '') {
        $errors[] = 'メールアドレスとパスワードは必須です。';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'メールアドレスの形式が正しくありません。';
    }

    if (empty($errors)) {
        try {
            $stmt = $dbh->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // --- 認証チェック ---
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: ../../index.php');
                exit;
            } else {
                $errors[] = 'メールアドレスまたはパスワードが違います。';
            }

        } catch (PDOException $e) {
            $errors[] = 'エラーが発生しました。もう一度お試しください。';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ログイン</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white min-h-screen flex justify-center items-center">

  <div class="text-center w-full max-w-xs">

    <h1 class="text-lg font-semibold mb-6">ログイン</h1>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form action="login.php" method="POST" class="space-y-4">

      <div class="flex items-center justify-between">
        <label class="text-sm">メール</label>
        <input
          type="email"
          name="email"
          required
          class="border w-48 px-2 py-1 rounded"
        >
      </div>

      <div class="flex items-center justify-between">
        <label class="text-sm">パスワード</label>
        <input
          type="password"
          name="password"
          required
          class="border w-48 px-2 py-1 rounded"
        >
      </div>

      <button
        type="submit"
        class="w-24 bg-blue-500 hover:bg-blue-600 text-white py-1 rounded"
      >
        ログイン
      </button>

    </form>

    <p class="text-sm mt-4">
      アカウントをお持ちでない方は
      <a href="signup.php" class="text-blue-500">新規登録</a>
    </p>

  </div>

</body>
</html>
