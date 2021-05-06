<?php
require_once '../shared/php/_helper.php';
if (!isTeacher() || $_GET['id'] == '') {
  header('Location: /');
  die();
}
$questions = null;
if (isset($_GET['id'])) {
  try {
    $pdo = getDBConnection();

    $statement = $pdo -> prepare('SELECT questions.question_title, questions.first_answer, questions.second_answer, questions.third_answer, questions.correct_answer FROM questions INNER JOIN quiz ON questions.FK_quiz_id_questions = quiz.quiz_id WHERE quiz.FK_teacher_id_quiz = :FK_teacher_id_quiz AND quiz.quiz_id = :quiz_id');
    $statement -> bindValue(':FK_teacher_id_quiz', getTeacherId(), PDO::PARAM_INT);
    $statement -> bindValue(':quiz_id', $_GET['id'], PDO::PARAM_INT);
    $statement -> execute();

    $questions = $statement -> fetchAll();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $statement = $pdo -> prepare('SELECT question_id FROM questions WHERE FK_quiz_id_questions = :FK_quiz_id_questions');
      $statement -> bindValue(':FK_quiz_id_questions', $_GET['id'], PDO::PARAM_INT);
      $statement -> execute();
      $result = $statement -> fetchAll();
      if ($result) {
        foreach ($questions as $i => $value) {
          $statement = $pdo -> prepare('UPDATE questions SET question_title = :question_title, first_answer = :first_answer, second_answer = :second_answer, third_answer = :third_answer, correct_answer = :correct_answer WHERE question_id = :question_id');
          $statement -> bindValue(':question_title', $_POST['title-' . $i], PDO::PARAM_STR);
          $statement -> bindValue(':first_answer', $_POST['first-answer-' . $i], PDO::PARAM_STR);
          $statement -> bindValue(':second_answer', $_POST['second-answer-' . $i], PDO::PARAM_STR);
          $statement -> bindValue(':third_answer', $_POST['third-answer-' . $i], PDO::PARAM_STR);
          $statement -> bindValue(':correct_answer', $_POST['correct-answer-' . $i], PDO::PARAM_INT);
          $statement -> bindValue(':question_id', $result[$i]['question_id'], PDO::PARAM_INT);
          $statement -> execute();
        }
      }
      header('Location: /account/teacher.php');
      die();
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
      $title = 'Add your questions';
      require_once '../shared/php/_head.php';
    ?>
  </head>
  <body>
    <?php require_once '../shared/php/_nav_bar.php'; ?>

    <div class="main-content">
      <form class="form" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST">
        <h3 class="title">Edit your questions and answers</h3>
        <?php foreach ($questions as $i => $value): ?>
          <div class="question">
            <input class="input title" type="text" value="<?php echo $value['question_title']; ?>" placeholder="Write title for question <?php echo $i; ?>" name="title-<?php echo $i; ?>">
            <div class="answers">
              <input class="input answer" type="text" value="<?php echo $value['first_answer']; ?>" placeholder="Write first answer" name="first-answer-<?php echo $i; ?>">
              <input class="input answer" type="text" value="<?php echo $value['second_answer']; ?>" placeholder="Write second answer" name="second-answer-<?php echo $i; ?>">
              <input class="input answer" type="text" value="<?php echo $value['third_answer']; ?>" placeholder="Write third answer" name="third-answer-<?php echo $i; ?>">
            </div>
            <div class="correct-answer">
              <h4 class="title">Choose the correct answer</h4>
              <input type="radio" name="correct-answer-<?php echo $i; ?>" value="1" <?php if ($value['correct_answer'] == 1) echo 'checked';?>>
              <label for="male">First answer</label>
              <input type="radio" name="correct-answer-<?php echo $i; ?>" value="2" <?php if ($value['correct_answer'] == 2) echo 'checked';?>>
              <label for="female">Second answer</label>
              <input type="radio" name="correct-answer-<?php echo $i; ?>" value="3" <?php if ($value['correct_answer'] == 3) echo 'checked';?>>
              <label for="other">Third answer</label>
            </div>
          </div>
        <?php endforeach ?>
        <input class="input full-width" type="submit" name="submit" value="Update quiz">
      </form>
    </div>

    <?php require_once '../shared/php/_footer.php'; ?>
  </body>
</html>
