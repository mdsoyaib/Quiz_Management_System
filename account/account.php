<?php
require_once '../shared/php/_helper.php';
if (isLoggedIn()) {
  try {
    $pdo = getDBConnection();
    $statement = $pdo -> prepare('SELECT user_unique_id FROM users WHERE LOWER(email_addr) = LOWER(:email_addr)');
    $statement -> bindValue(':email_addr', getEmailFromCookie(), PDO::PARAM_STR);
    $statement -> execute();
    $result = $statement -> fetchAll();
    if ($result) {
      if ($result[0]['user_unique_id'] == getUIDFromCookie()) {
        header('Location: /account/student.php');
        die();
      }
    } else {
      $statement = $pdo -> prepare('SELECT teacher_unique_id FROM teachers WHERE LOWER(email_addr) = LOWER(:email_addr)');
      $statement -> bindValue(':email_addr', getEmailFromCookie(), PDO::PARAM_STR);
      $statement -> execute();
      $result = $statement -> fetchAll();
      if ($result) {
        if ($result[0]['teacher_unique_id'] == getUIDFromCookie()) {
          header('Location: /account/teacher.php');
          die();
        }
      }
    }
    $pdo = null;
  } catch (PDOException $e) {
    writeLog($e -> getMessage());
  }
} else {
  header('Location: /account/signin.php');
  die();
}
?>
