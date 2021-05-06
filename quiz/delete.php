<?php
require_once '../shared/php/_helper.php';
if ($_GET['id'] != '') {
  try {
    $pdo = getDBConnection();
    $statement = $pdo -> prepare('DELETE FROM quiz WHERE quiz_id = :quiz_id');
    $statement -> bindValue(':quiz_id', $_GET['id'], PDO::PARAM_INT);
    $statement -> execute();
    $pdo = null;
  } catch (PDOException $e) {
    writeLog($e -> getMessage());
  }
}
header('Location: /account/teacher.php');
die();
?>
