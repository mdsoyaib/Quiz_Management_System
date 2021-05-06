<?php
require_once '../shared/php/_helper.php';
if (!isLoggedIn() || !isset($_GET['id'])) {
  header('Location: /');
  die();
}
$result = '';
$totalCorrected = 0;
try {
  $pdo = getDBConnection();
  if (isTeacher()) {
    $statement = $pdo -> prepare('SELECT users.user_id, users.first_name, users.last_name FROM ((quiz INNER JOIN users ON quiz.FK_teacher_id_quiz = users.FK_teacher_id_users) INNER JOIN teachers ON users.FK_teacher_id_users = teachers.teacher_id) WHERE teachers.teacher_unique_id = :teacher_unique_id AND quiz.quiz_id = :quiz_id');
    $statement -> bindValue(':teacher_unique_id', getUIDFromCookie(), PDO::PARAM_STR);
    $statement -> bindValue(':quiz_id', $_GET['id'], PDO::PARAM_INT);
    $statement -> execute();
    $result = $statement -> fetchAll();
  } else {
    $statement = $pdo -> prepare('SELECT questions.question_title, CASE WHEN answers.answer = 1 THEN questions.first_answer WHEN answers.answer = 2 THEN questions.second_answer WHEN answers.answer = 3 THEN questions.third_answer END AS user_answer, CASE WHEN questions.correct_answer = 1 THEN questions.first_answer WHEN questions.correct_answer = 2 THEN questions.second_answer WHEN questions.correct_answer = 3 THEN questions.third_answer END AS correct_answer, IF (questions.correct_answer = answers.answer, "1", "0") AS is_correct FROM ((questions INNER JOIN answers ON questions.question_id = answers.FK_questions_id_answers) INNER JOIN users ON answers.FK_user_id_answers = users.user_id) WHERE answers.FK_quiz_id_answers = :FK_quiz_id_answers AND users.user_unique_id = :user_unique_id');
    $statement -> bindValue(':FK_quiz_id_answers', $_GET['id'], PDO::PARAM_INT);
    $statement -> bindValue(':user_unique_id', getUIDFromCookie(), PDO::PARAM_STR);
    $statement -> execute();
    $result = $statement -> fetchAll();
  }
  $pdo = null;
} catch (PDOException $e) {
  writeLog($e -> getMessage());
}
?>
<!DOCTYPE HTML>
<html lang="en-US">
  <head>
    <?php
      $title = 'Quiz Result';
      require_once '../shared/php/_head.php';
    ?>
  </head>
  <body>
    <?php require_once '../shared/php/_nav_bar.php'; ?>
      <div class="main-content">
        <h3>Result</h3>
        <?php if (isTeacher()): ?>
          <table class="table">
            <tr>
              <th>No.</th>
              <th>Student Name</th>
              <th>Result</th>
            </tr>
            <?php foreach ($result as $key => $value): ?>
              <tr>
                <td><?php echo $key + 1; ?></td>
                <td><?php echo $value['first_name'] . ' ' . $value['last_name']; ?></td>
                <td><?php echo getResultForStudent($value['user_id'], $_GET['id']); ?></td>
              </tr>
            <?php endforeach ?>
          </table>
        <?php else: ?>
          <table class="table">
            <tr>
              <th>No.</th>
              <th>Question</th>
              <th>Your answer</th>
              <th>Correct answer</th>
            </tr>
            <?php foreach ($result as $key => $value): ?>
              <tr <?php if ($value['is_correct'] == 0) {
                  echo 'style="color: red;"';
                } else {
                  ++$totalCorrected;
                }?>>
                <td><?php echo $key + 1; ?></td>
                <td><?php echo $value['question_title']; ?></td>
                <td><?php echo $value['user_answer']; ?></td>
                <td><?php echo $value['correct_answer']; ?></td>
              </tr>
            <?php endforeach ?>
          </table>
          <p>Total Correct Answer: <?php echo $totalCorrected; ?></p>
        <?php endif ?>
      </div>
    <?php require_once '../shared/php/_footer.php'; ?>
  </body>
</html>
