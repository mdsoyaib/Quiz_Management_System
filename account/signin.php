<?php
require_once '../shared/php/_helper.php';
if (isset($_POST['email-addr']) && isset($_POST['password'])) {
  try {
    $pdo = getDBConnection();
    $statement = $pdo -> prepare('SELECT user_unique_id, password_hash FROM users WHERE LOWER(email_addr) = LOWER(:email_addr)');
    $statement -> bindValue(':email_addr', $_POST['email-addr'], PDO::PARAM_STR);
    $statement -> execute();
    $result = $statement -> fetchAll();
    if ($result) {
      if (password_verify($_POST['password'], $result[0]['password_hash'])) {
        setCookieHash($result[0]['user_unique_id'], $_POST['email-addr'], 1);
        header('Location: /account/account.php');
        die();
      } else {
        showAlert('Email address and password did not match');
      }
    } else {
      $statement = $pdo -> prepare('SELECT teacher_unique_id, password_hash FROM teachers WHERE LOWER(email_addr) = LOWER(:email_addr)');
      $statement -> bindValue(':email_addr', $_POST['email-addr'], PDO::PARAM_STR);
      $statement -> execute();
      $result = $statement -> fetchAll();
      if ($result) {
        if (password_verify($_POST['password'], $result[0]['password_hash'])) {
          setCookieHash($result[0]['teacher_unique_id'], $_POST['email-addr'], 1);
          header('Location: /account/account.php');
          die();
        } else {
          showAlert('Email address and password did not match');
        }
      }
    }
    $pdo = null;
  } catch (PDOException $e) {
    writeLog($e -> getMessage());
  }
}
?>

<!DOCTYPE HTML>
<html lang="en-US">
  <head>
    <?php
      $title = 'Sign in';
      require_once '../shared/php/_head.php';
    ?>
  </head>
  <body>
    <?php require_once '../shared/php/_nav_bar.php'; ?>
    <div class="main-content">
      <form class="form max-width-500" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST">
        <h3 class="title">Sign in</h3>
        <input class="input full-width" type="email" placeholder="Enter your email address" name="email-addr" autofocus>
        <input class="input full-width" type="password" placeholder="Enter your password" name="password">
        <input class="input full-width" type="submit" name="submit" value="Sign in to your account">
        <a class="secondary-button" href="/account/signup.php">Create new account</a>
      </form>
    </div>
    <?php require_once '../shared/php/_footer.php'; ?>
  </body>
</html>
