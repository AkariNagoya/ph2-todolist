<?php
require_once '../../dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  $stmt = $dbh->prepare('DELETE FROM todos WHERE id = :id');
  $stmt->bindValue(':id', $_POST['id']);
  $stmt->execute();
  header("Location: ../../index.php");
  exit;
} 
?>