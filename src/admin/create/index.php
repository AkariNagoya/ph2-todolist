<?php
require_once '../../dbconnect.php';
session_start(); 

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    // ログインしていない場合はトップに戻す
    header('Location: ../../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php');
    exit;
}

$text = trim($_POST['text'] ?? '');

if (empty($text)) {
    header('Location: ../../index.php');
    exit;
}

try{
  $stmt = $dbh->prepare('INSERT INTO todos(text, complete, user_id) VALUES (:text, :complete, :user_id)');
  $stmt->bindValue(':text', $text, PDO::PARAM_STR);
  $stmt->bindValue(':complete', 0, PDO::PARAM_BOOL);
  $stmt->bindValue(':user_id', $userId,  PDO::PARAM_INT);
  $stmt->execute();
}catch(PDOException $e){
  echo 'エラー:'. htmlspecialchars($e->getMessage());
  exit;
}

header('Location: ../../index.php');
exit;
?>