<?php
$PROJECT_NAME = 'Quiz Management System';

date_default_timezone_set('Asia/Dhaka');

function getValidData($data = '') {
  $data = trim($data);
  $data = stripcslashes($data);
  $data = htmlspecialchars($data, ENT_QUOTES);
  return $data;
}

function getCheckboxData($data = '') {
  if ($data === 'on') {
    return true;
  }
  return false;
}

function getValidPhoneNum($value='') {
  if (!preg_match('/^\+?(88)?01[3456789][0-9]{8}\b/', $value)) {
    return;
  }
  if (substr($value, 0, 2) == '01') {
    $value = '+88'.$value;
  }
  if (substr($value, 0, 2) == '88') {
    $value = '+'.$value;
  }
  return $value;
}

function getDBConnection() {
  $secureXML = findLocation('shared/secure/', '.secure.xml');
  $dbStrings = simplexml_load_file($secureXML);
  $servername = $dbStrings -> db -> servername;
  $username = $dbStrings -> db -> username;
  $password = $dbStrings -> db -> password;
  $dbName = $dbStrings -> db -> db_name;
  try {
    $conn = new PDO('mysql:host='.$servername.';dbname='.$dbName, $username, $password);
    $conn-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn -> exec("set names utf8");
    return $conn;
  } catch (PDOException $e) {
    writeLog('Connection failed: '.$e -> getMessage());
    return null;
  }
}

function writeLog($string = '') {
  date_default_timezone_set('Asia/Dhaka');
  $currentDateTime = date('d-M-Y h:i:s A', time());
  $caller = array_shift(debug_backtrace());
  $txt = '['.$currentDateTime.', ' . $caller['file'] . ':' . $caller['line'] . ']' . "\n" . '>> ' . $string . "\n";
  $logFile = findLocation('log/', 'site.log');
  error_log($txt, 3, $logFile);
}

function findLocation($folderName, $fileName)
{
  if (substr($folderName, 0, 1) === '/') {
    $folderName = substr($folderName, 1);
  }
  if (substr($folderName, -0, -1) !== '/') {
    $folderName = $folderName.'/';
  }
  $counter = 0;
  while ($counter < 25) {
    if (is_dir($folderName)) {
      return $folderName.$fileName;
    }
    $counter++;
    $folderName = '../'.$folderName;
  }
}

function getEmailFromCookie($authName = 'u_email') {
  if (isset($_COOKIE[$authName])) {
    if ($_COOKIE[$authName] !== 'null') {
      return $_COOKIE[$authName];
    }
  }
  if (!isset($_SESSION)) {
    session_start();
  }
  if (isset($_SESSION[$authName])) {
    if ($_SESSION[$authName] !== 'null') {
      return $_SESSION[$authName];
    }
  }
}

function getUIDFromCookie($authName = 'u_auth') {
  $uid = '';
  if (isset($_COOKIE[$authName]) && $_COOKIE[$authName] != 'null') {
    $cookieHash = $_COOKIE[$authName];
    $uid = substr($cookieHash, 21, 16);
    return $uid;
  } else {
    if (!isset($_SESSION)) {
    session_start();
  }
    if (isset($_SESSION[$authName]) && $_SESSION[$authName] != 'null') {
      $sessionHash = $_SESSION[$authName];
      $uid = substr($sessionHash, 21, 16);
      return $uid;
    }
  }
  return null;
}

function setCookieHash($uniqueId, $email, $rememberMe) {
  $options = [
    'cost' => 6,
  ];
  $uniqueIdHash = password_hash($uniqueId, PASSWORD_DEFAULT, $options);
  $prependHash = password_hash($uniqueIdHash, PASSWORD_DEFAULT, $options);
  $prependHash = substr($prependHash, 7, 21);
  $hashToSave = $prependHash.$uniqueId.$uniqueIdHash;
  if (!isset($_SESSION)) {
    session_start();
  }
  if ($rememberMe === '1') {
    $_SESSION['u_auth'] = 'null';
    $_SESSION['u_email'] = 'null';
    setcookie('u_auth', $hashToSave, time() + (86400 * 2000), '/');
    setcookie('u_email', $email, time() + (86400 * 2000), '/');
  } else {
    setcookie('u_auth', 'null', 86400, '/');
    setcookie('u_email', 'null', 86400, '/');
    $_SESSION['u_auth'] = $hashToSave;
    $_SESSION['u_email'] = $email;
  }
}

function isLoggedIn($authName = 'u_auth') {
  if (isset($_COOKIE[$authName]) && $_COOKIE[$authName] != 'null') {
    $cookieHash = $_COOKIE[$authName];
    $uid = substr($cookieHash, 21, 16);
    $uidHash = substr($cookieHash, 37);
    if (password_verify($uid, $uidHash)) {
      try {
        $pdo = getDBConnection();
        $email = getEmailFromCookie();
        $statement = $pdo -> prepare('SELECT user_unique_id FROM users WHERE LOWER(email_addr) = LOWER(:email)');
        $statement -> bindValue(':email', $email, PDO::PARAM_STR);
        $statement -> execute();
        $result = $statement -> fetchAll();
        $pdo = null;
        if ($result) {
          if ($uid == $result[0]['user_unique_id']) {
            return true;
          }
        } else {
          $pdo = getDBConnection();
          $statement = $pdo -> prepare('SELECT teacher_unique_id FROM teachers WHERE LOWER(email_addr) = LOWER(:email)');
          $statement -> bindValue(':email', $email, PDO::PARAM_STR);
          $statement -> execute();
          $result = $statement -> fetchAll();
          $pdo = null;
          if ($result) {
            if ($uid == $result[0]['teacher_unique_id']) {
              return true;
            }
          }
        }
      } catch (PDOException $e) {
        writeLog($e -> getMessage());
      }
    }
  }
  if (!isset($_SESSION)) {
    session_start();
  }
  if (isset($_SESSION[$authName]) && $_SESSION[$authName] != 'null') {
    $sessionHash = $_SESSION[$authName];
    $uid = substr($sessionHash, 21, 16);
    $uidHash = substr($sessionHash, 37);
    if (password_verify($uid, $uidHash)) {
      try {
        $pdo = getDBConnection();
        $email = getEmailFromCookie();
        $statement = $pdo -> prepare('SELECT user_unique_id FROM users WHERE LOWER(email_addr) = LOWER(:email)');
        $statement -> bindValue(':email', $email, PDO::PARAM_STR);
        $statement -> execute();
        $result = $statement -> fetchAll();
        $pdo = null;
        if ($result) {
          if ($uid == $result[0]['user_unique_id']) {
            return true;
          }
        } else {
          $pdo = getDBConnection();
          $statement = $pdo -> prepare('SELECT teacher_unique_id FROM teachers WHERE LOWER(email_addr) = LOWER(:email)');
          $statement -> bindValue(':email', $email, PDO::PARAM_STR);
          $statement -> execute();
          $result = $statement -> fetchAll();
          $pdo = null;
          if ($result) {
            if ($uid == $result[0]['teacher_unique_id']) {
              return true;
            }
          }
        }
      } catch (PDOException $e) {
        writeLog($e -> getMessage());
      }
    }
  }
  return false;
}

function isTeacher() {
  try {
    $pdo = getDBConnection();
    $statement = $pdo -> prepare('SELECT teacher_unique_id FROM teachers WHERE LOWER(email_addr) = LOWER(:email)');
    $statement -> bindValue(':email', getEmailFromCookie(), PDO::PARAM_STR);
    $statement -> execute();
    $result = $statement -> fetchAll();
    $pdo = null;
    if ($result) {
      if (getUIDFromCookie() == $result[0]['teacher_unique_id']) {
        return true;
      }
    }
  } catch (PDOException $e) {
    writeLog($e -> getMessage());
  }
}

function getFullName() {
  if (isLoggedIn()) {
    $userUniqueId = getUIDFromCookie();
    try {
      $pdo = getDBConnection();
      $statement = $pdo -> prepare('SELECT first_name, last_name FROM users WHERE user_unique_id = :user_unique_id');
      $statement -> bindValue(':user_unique_id', $userUniqueId, PDO::PARAM_STR);
      $statement -> execute();
      $result = $statement -> fetchAll();
      $pdo = null;
      if ($result) {
        return $result[0]['first_name'] . ' ' . $result[0]['last_name'];
      }
    } catch (PDOException $e) {
      writeLog($e -> getMessage());
    }
  }
}

function getTeacherId() {
  if (isLoggedIn() && isTeacher()) {
    try {
      $pdo = getDBConnection();
      $statement = $pdo -> prepare('SELECT teacher_id FROM teachers WHERE teacher_unique_id = :teacher_unique_id');
      $statement -> bindValue(':teacher_unique_id', getUIDFromCookie(), PDO::PARAM_STR);
      $statement -> execute();
      $result = $statement -> fetchAll();
      $pdo = null;
      if ($result) {
        return $result[0]['teacher_id'];
      }
    } catch (PDOException $e) {
      writeLog($e -> getMessage());
    }
  }
}

function isSubmitted($quizId = '') {
  try {
    $pdo = getDBConnection();
    $statement = $pdo -> prepare('SELECT answer_id FROM answers INNER JOIN users ON FK_user_id_answers = users.user_id WHERE users.user_unique_id = :user_unique_id AND FK_quiz_id_answers = :FK_quiz_id_answers');
    $statement -> bindValue(':user_unique_id', getUIDFromCookie(), PDO::PARAM_STR);
    $statement -> bindValue(':FK_quiz_id_answers', $quizId, PDO::PARAM_INT);
    $statement -> execute();
    $result = $statement -> fetchAll();
    $pdo = null;
    if ($result) {
      return true;
    }
  } catch (PDOException $e) {
    writeLog($e -> getMessage());
  }
  return false;
}

function getResultForStudent($studentId = '', $quizId = '') {
  try {
    $pdo = getDBConnection();
    $statement = $pdo -> prepare('SELECT COUNT(CASE WHEN questions.correct_answer = answers.answer THEN 1 END) AS total_correct_answer FROM answers INNER JOIN questions ON answers.FK_questions_id_answers = questions.question_id WHERE answers.FK_user_id_answers = :FK_user_id_answers AND answers.FK_quiz_id_answers = :FK_quiz_id_answers');
    $statement -> bindValue(':FK_user_id_answers', $studentId, PDO::PARAM_INT);
    $statement -> bindValue(':FK_quiz_id_answers', $quizId, PDO::PARAM_INT);
    $statement -> execute();
    $result = $statement -> fetchAll();
    if ($result) {
      return $result[0]['total_correct_answer'];
    }
    $pdo = null;
  } catch (PDOException $e) {
    writeLog($e -> getMessage());
  }
}

function showAlert($value='') {
  echo '<script language="javascript">';
  echo 'alert("'. $value . '")';
  echo '</script>';
}
?>
