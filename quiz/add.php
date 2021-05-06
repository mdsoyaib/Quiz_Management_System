<?php
require_once '../shared/php/_helper.php';
if (!isTeacher()) {
  header('Location: /');
  die();
}

if (!isset($_GET['title']) || !isset($_GET['course']) || !isset($_GET['section']) || !isset($_GET['num_of_question']) || !isset($_GET['time'])) {
  header('Location: /quiz/create.php');
  die();
}
$title = $_GET['title'];
$course = $_GET['course'];
$section = $_GET['section'];
$numberOfQuestion = $_GET['num_of_question'];
$time = $_GET['time'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  try {
    $pdo = getDBConnection();
    $statement = $pdo -> prepare('INSERT INTO quiz (title, course, section, number_of_question, quiz_time, FK_teacher_id_quiz) VALUES (:title, :course, :section, :number_of_question, :quiz_time, :FK_teacher_id_quiz)');
    $statement -> bindValue(':title', $title, PDO::PARAM_STR);
    $statement -> bindValue(':course', $course, PDO::PARAM_STR);
    $statement -> bindValue(':section', $section, PDO::PARAM_STR);
    $statement -> bindValue(':number_of_question', $numberOfQuestion, PDO::PARAM_STR);
    $statement -> bindValue(':quiz_time', $time, PDO::PARAM_STR);
    $statement -> bindValue(':FK_teacher_id_quiz', getTeacherId(), PDO::PARAM_INT);
    $statement -> execute();

    $lastId = $pdo -> lastInsertId();
    $sqlQuery = '';
    for ($i = 0; $i < $numberOfQuestion; ++$i) {
      $sqlQuery .= '("' . $_POST['title-' . ($i + 1)] . '", "' . $_POST['first-answer-' . ($i + 1)] . '", "' . $_POST['second-answer-' . ($i + 1)] . '", "' . $_POST['third-answer-' . ($i + 1)] . '", "' . $_POST['correct-answer-' . ($i + 1)] . '", "' . $lastId . '")';
      if ($i < $numberOfQuestion - 1) {
        $sqlQuery .= ', ';
      }
    }
    $statement = $pdo -> prepare('INSERT INTO questions (question_title, first_answer, second_answer, third_answer,correct_answer, FK_quiz_id_questions) VALUES ' . $sqlQuery);
    $statement -> execute();
    $pdo = null;
    header('Location: /account/teacher.php');
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
      $title = 'Add your questions';
      require_once '../shared/php/_head.php';
    ?>
  </head>
  <body>
    <?php require_once '../shared/php/_nav_bar.php'; ?>

    <div class="main-content">
      <form class="form" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST">
        <h3 class="title">Add your questions and answers</h3>
        <?php for ($i = 1; $i <= $numberOfQuestion; ++$i): ?>
          <div class="question">
            <input class="input title" type="text" placeholder="Write title for question <?php echo $i; ?>" name="title-<?php echo $i; ?>">
            <div class="answers">
              <input class="input answer" type="text" placeholder="Write first answer" name="first-answer-<?php echo $i; ?>">
              <input class="input answer" type="text" placeholder="Write second answer" name="second-answer-<?php echo $i; ?>">
              <input class="input answer" type="text" placeholder="Write third answer" name="third-answer-<?php echo $i; ?>">
            </div>
            <div class="correct-answer">
              <h4 class="title">Choose the correct answer</h4>
              <input type="radio" name="correct-answer-<?php echo $i; ?>" value="1">
              <label for="male">First answer</label>
              <input type="radio" name="correct-answer-<?php echo $i; ?>" value="2">
              <label for="female">Second answer</label>
              <input type="radio" name="correct-answer-<?php echo $i; ?>" value="3">
              <label for="other">Third answer</label>
            </div>
          </div>
        <?php endfor ?>
        <input class="input full-width" type="submit" name="submit" value="Create quiz">
      </form>
    </div>

    <?php require_once '../shared/php/_footer.php'; ?>
  </body>
</html>
