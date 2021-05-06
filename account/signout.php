<?php
  session_start();
  $_SESSION['u_auth'] = 'null';
  $_SESSION['u_name'] = 'null';
  setcookie('u_auth', 'null', time() + (86400 * 2000), '/');
  setcookie('u_name', 'null', time() + (86400 * 2000), '/');
  header('Location: /');
  die();
?>
