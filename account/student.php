<?php
require_once '../shared/php/_helper.php';
if (!isLoggedIn()) {
  header('Location: /account/signin.php');
  die();
}
$quiz = '';
try {
  $pdo = getDBConnection();
  $statement = $pdo -> prepare('SELECT quiz.quiz_id, quiz.title, quiz.course, quiz.section, quiz.number_of_question, quiz.quiz_time FROM quiz INNER JOIN users ON users.FK_teacher_id_users = quiz.FK_teacher_id_quiz WHERE users.user_unique_id = :user_unique_id');
  $statement -> bindValue(':user_unique_id', getUIDFromCookie(), PDO::PARAM_STR);
  $statement -> execute();
  $quiz = $statement -> fetchAll();
  $pdo = null;
} catch (PDOException $e) {
  writeLog($e -> getMessage());
}
?>
<!DOCTYPE HTML>
<html lang="en-US">
  <head>
    <?php
      $title = 'Student account';
      require_once '../shared/php/_head.php';
    ?>
  </head>
  <body>
    <?php require_once '../shared/php/_nav_bar.php'; ?>

    <div class="main-content">
        <table class="table quiz">
          <tr>
            <th>SL</th>
            <th>Title</th>
            <th>Course</th>
            <th>Section</th>
            <th>Number of question</th>
            <th>Time</th>
            <th>Action</th>
          </tr>
          <?php foreach ($quiz as $key => $value): ?>
            <tr>
              <td><?php echo $key + 1; ?></td>
              <td><?php echo $value['title']; ?></td>
              <td><?php echo $value['course']; ?></td>
              <td><?php echo $value['section']; ?></td>
              <td><?php echo $value['number_of_question']; ?></td>
              <td><?php echo $value['quiz_time']; ?></td>
              <?php if (isSubmitted($value['quiz_id'])): ?>
                <td><a href="/quiz/result.php?id=<?php echo $value['quiz_id']; ?>">Result</a></td>
              <?php else: ?>
                <td><a href="/quiz/answer.php?id=<?php echo $value['quiz_id']; ?>">Start</a></td>
              <?php endif ?>
            </tr>
          <?php endforeach ?>
        </table>
    </div>

    <?php require_once '../shared/php/_footer.php'; ?>
  </body>
</html>
