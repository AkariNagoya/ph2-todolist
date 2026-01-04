<?php
require_once '../../dbconnect.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // ---- バリデーション ----
    if ($email === '' || $password === '') {
        $errors[] = 'メールとパスワードは必須です。';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'メールアドレスの形式が正しくありません。';
    }

    if (empty($errors)) {
        try {
            // すでに登録されていないか確認
            $stmt = $dbh->prepare('SELECT id FROM users WHERE email = :email');
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->fetch()) {
                $errors[] = 'このメールアドレスは既に登録されています。';
            } else {
                // パスワードは必ずハッシュ化
                $hashed = password_hash($password, PASSWORD_DEFAULT);

                $insert = $dbh->prepare('INSERT INTO users (email, password) VALUES (:email, :password)');
                $insert->bindValue(':email', $email, PDO::PARAM_STR);
                $insert->bindValue(':password', $hashed, PDO::PARAM_STR);
                $insert->execute();

                // 正常ならログイン画面へ
                header('Location: login.php');
                exit;
            }

        } catch (PDOException $e) {
            $errors[] = '登録エラー: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>新規登録</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white min-h-screen flex justify-center items-center">

  <div class="text-center w-full max-w-xs">

    <h1 class="text-lg font-semibold mb-6">新規登録</h1>

    <form action="./signup.php" method="POST" class="space-y-4">

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
        登録
      </button>

    </form>

    <p class="text-sm mt-4">
      すでにアカウントをお持ちですか？
      <a href="./login.php" class="text-blue-500">ログイン</a>
    </p>

  </div>

</body>
</html>
