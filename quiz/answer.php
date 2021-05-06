<?php
require_once '../shared/php/_helper.php';
if (!isLoggedIn() || $_GET['id'] == '') {
  header('Location: /');
  die();
}
$questions = '';
try {
  $pdo = getDBConnection();
  $statement = $pdo -> prepare('SELECT quiz.title, quiz.quiz_time, questions.question_id, questions.question_title, questions.first_answer, questions.second_answer, questions.third_answer, users.user_id FROM (((questions INNER JOIN quiz ON questions.FK_quiz_id_questions = quiz.quiz_id) INNER JOIN teachers ON quiz.FK_teacher_id_quiz = teachers.teacher_id) INNER JOIN users ON users.FK_teacher_id_users = teachers.teacher_id) WHERE users.user_unique_id = :user_unique_id AND quiz.quiz_id = :quiz_id');
  $statement -> bindValue(':user_unique_id', getUIDFromCookie(), PDO::PARAM_STR);
  $statement -> bindValue(':quiz_id', $_GET['id'], PDO::PARAM_INT);
  $statement -> execute();
  $questions = $statement -> fetchAll();
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sqlQuery = '';
    foreach ($questions as $i => $value) {
      $sqlQuery .= '("' . $_POST['answer-' . $i] . '", "' . $value['user_id'] . '", "' . $_GET['id'] . '", "' . $value['question_id'] .'")';
      if ($i < count($questions) - 1) {
        $sqlQuery .= ', ';
      }
    }
    $statement = $pdo -> prepare('INSERT INTO answers (answers.answer, answers.FK_user_id_answers, answers.FK_quiz_id_answers, answers.FK_questions_id_answers) VALUES ' . $sqlQuery);
    $statement -> execute();
    $pdo = null;
    header('Location: /quiz/result.php?id=' . $_GET['id']);
    die();
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
      $title = 'Answer questions';
      require_once '../shared/php/_head.php';
    ?>
  </head>
  <body>
    <?php require_once '../shared/php/_nav_bar.php'; ?>
    <input type="hidden" id="duration" value="<?php echo $questions[0]['quiz_time']; ?>">
    <input type="hidden" id="quiz-id" value="<?php echo $_GET['id']; ?>">
      <div class="main-content">
        <form id="answer-form" class="form max-width-500" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST">
          <h3 class="title"><?php echo $questions[0]['title']; ?></h3>
          <p id="count-down-timer" style="color: #fff; margin-bottom: 16px;"></p>
          <?php foreach ($questions as $i => $value): ?>
            <p style="color: #fff;"><?php echo $value['question_title']; ?></p>
            <input type="radio" name="answer-<?php echo $i; ?>" value="1">
            <label for="male"><?php echo $value['first_answer']; ?></label>
            <input type="radio" name="answer-<?php echo $i; ?>" value="2">
            <label for="female"><?php echo $value['second_answer']; ?></label>
            <input type="radio" name="answer-<?php echo $i; ?>" value="3">
            <label for="other"><?php echo $value['third_answer']; ?></label>
          <?php endforeach ?>
          <input class="input full-width" type="submit" value="Submit">
        </form>
      </div>
    <?php require_once '../shared/php/_footer.php'; ?>


    <script type="text/javascript">
      var duration = document.getElementById('duration');
      var d1 = new Date ();
      var d2 = new Date (d1);
      d2.setMinutes ( d1.getMinutes() + parseInt(duration.value));
      var countDownDate = d2;
      var x = setInterval(function() {
        var now = new Date().getTime();
        var distance = countDownDate - now;
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        document.getElementById("count-down-timer").innerHTML = hours + "h "
        + minutes + "m " + seconds + "s";
        if (distance < 0) {
          clearInterval(x);
          document.getElementById('answer-form').submit();
        }
      }, 1000);
    </script>
  </body>
</html>
