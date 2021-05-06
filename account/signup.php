<?php
require_once '../shared/php/_helper.php';
if (isset($_POST['first-name']) && isset($_POST['last-name']) && isset($_POST['student-id']) &&
    isset($_POST['email-addr']) && isset($_POST['password']) && isset($_POST['confirm-password']) && $_POST['password'] == $_POST['confirm-password']) {
  try {
    $options = [
      'cost' => 12,
    ];
    $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT, $options);
    $pdo = getDBConnection();
    $statement = $pdo -> prepare('INSERT INTO users (first_name, last_name, student_id, email_addr, password_hash, FK_teacher_id_users) VALUES (:first_name, :last_name, :student_id, :email_addr, :password_hash, :FK_teacher_id_users)');
    $statement -> bindValue(':first_name', $_POST['first-name'], PDO::PARAM_STR);
    $statement -> bindValue(':last_name', $_POST['last-name'], PDO::PARAM_STR);
    $statement -> bindValue(':student_id', $_POST['student-id'], PDO::PARAM_STR);
    $statement -> bindValue(':email_addr', $_POST['email-addr'], PDO::PARAM_STR);
    $statement -> bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
    $statement -> bindValue(':FK_teacher_id_users', 1, PDO::PARAM_INT);
    $statement -> execute();

    $lastId = $pdo -> lastInsertId();
    $userUniqueId = uniqid($lastId);
    while (strlen($userUniqueId) < 16) {
      $userUniqueId .= uniqid();
    }
    $userUniqueId = substr($userUniqueId, 0, 16);

    $statement = $pdo -> prepare('UPDATE users SET user_unique_id = :user_unique_id WHERE user_id = :user_id');
    $statement -> bindValue(':user_unique_id', $userUniqueId, PDO::PARAM_STR);
    $statement -> bindValue(':user_id', $lastId, PDO::PARAM_INT);
    $statement -> execute();
    setCookieHash($userUniqueId, $_POST['email-addr'], 1);
    $pdo = null;
    header('Location: /account/account.php');
    die();
  } catch (PDOException $e) {
    writeLog($e -> getMessage());
  }
}
?>

<!DOCTYPE HTML>
<html lang="en-US">
  <head>
    <?php
      $title = 'Sign up';
      require_once '../shared/php/_head.php';
    ?>
  </head>
  <body>
    <?php require_once '../shared/php/_nav_bar.php'; ?>

    <div class="main-content">
      <form class="form max-width-500" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST">
        <h3 class="title">Create New Account</h3>
        <input class="input full-width" type="text" placeholder="Enter your first name" name="first-name" autofocus>
        <input class="input full-width" type="text" placeholder="Enter your last name" name="last-name">
        <input class="input full-width" type="text" placeholder="Enter your student ID" name="student-id">
        <input class="input full-width" type="email" placeholder="Enter your email address" name="email-addr">
        <input class="input full-width" type="password" placeholder="Enter your password" name="password">
        <input class="input full-width" type="password" placeholder="Confirm your password" name="confirm-password">
        <input class="input full-width" type="submit" name="submit" value="Create new account">
        <a class="secondary-button" href="/account/signin.php">Go back to sign in</a>
      </form>
    </div>

    <?php require_once '../shared/php/_footer.php'; ?>
  </body>
</html>
