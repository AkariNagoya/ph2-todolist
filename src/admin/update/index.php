<?php
require_once '../../dbconnect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

  $id = $_POST['id'] ?? '';

  if(!$id){
    echo json_encode(['status' => 'error']);
    exit;
  }

  // if (empty($id)) {
  //   header('Location: ../../index.php');
  //   exit;
  // }

  try{
    $stmt = $dbh->prepare('SELECT complete FROM todos WHERE id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $todos = $stmt->fetch(PDO::FETCH_ASSOC);

    // if (!$todos) {
    //       header('Location: ../../index.php');
    //       exit;
    // }

    $newStatus = !$todos['complete'] ? 1 : 0;
    $update = $dbh->prepare('UPDATE todos SET complete = :complete WHERE id = :id');
    $update->bindValue(':complete', $newStatus, PDO::PARAM_INT);
    $update->bindValue(':id', $id, PDO::PARAM_INT);
    $update->execute();

    echo json_encode(['id' => $id, 'complete' => $newStatus
  ]);
  }catch (PDOException $e) {
    echo json_encode(['status' => 'error',
    'message' => $e->getMessage()
    ]);
      exit;
    // echo 'エラー: ' . htmlspecialchars($e->getMessage());
    // exit;
  }
}

// header('Location: ../../index.php');
exit;
?>