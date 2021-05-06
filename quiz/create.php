<?php
require_once '../shared/php/_helper.php';
if (!isTeacher()) {
  header('Location: /');
  die();
}
if (isset($_POST['quiz-title']) && isset($_POST['course-code']) && isset($_POST['section']) &&
    isset($_POST['nums']) && isset($_POST['quiz-time'])) {
  header('Location: /quiz/add.php?title=' . $_POST['quiz-title'] . '&course=' . $_POST['course-code'] .
          '&section=' . $_POST['section'] . '&num_of_question=' . $_POST['nums'] .
          '&time=' . $_POST['quiz-time']);
  die();
}
?>

<!DOCTYPE HTML>
<html lang="en-US">
  <head>
    <?php
      $title = 'Create questions';
      require_once '../shared/php/_head.php';
    ?>
  </head>
  <body>
    <?php require_once '../shared/php/_nav_bar.php'; ?>

    <div class="main-content">
      <form class="form max-width-500" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST">
        <h3 class="title">Create Question</h3>
        <input class="input full-width" type="text" placeholder="Enter Title" name="quiz-title" autofocus>
        <input class="input full-width" type="text" placeholder="Enter Course Code" name="course-code">
        <input class="input full-width" type="text" placeholder="Enter Section" name="section">
        <input class="input full-width" type="number" placeholder="Enter Number of Questions" name="nums">
        <input class="input full-width" type="number" placeholder="Enter Time in Minutes" name="quiz-time">
        <input class="input full-width" type="submit" value="Next">
      </form>
    </div>

    <?php require_once '../shared/php/_footer.php'; ?>
  </body>
</html>
