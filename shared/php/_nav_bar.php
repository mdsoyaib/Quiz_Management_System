<?php require_once '_helper.php'; ?>
<div class="topnav">
  <a class="title" href="/"><?php echo $PROJECT_NAME; ?></a>
  <nav>
    <?php if (isLoggedIn()): ?>
      <?php if (isTeacher()): ?>
        <a href="/quiz/create.php">Create a Quiz</a>
      <?php endif ?>
      <a href="/account/account.php">Account</a>
      <a href="/account/signout.php">Sign out</a>
    <?php else: ?>
      <a href="/account/signin.php">Sign in</a>
      <a href="/account/signup.php">Sign up</a>
    <?php endif ?>
  </nav>
</div>
