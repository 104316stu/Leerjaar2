<?php
session_start();
$_SESSION['logged_in'] = true;
$_SESSION['username'] = $_POST['username'];
$_SESSION['password'] = $_POST['password'];
$_SESSION['userid'] = 1;

header('Location: ../index.php');
exit;