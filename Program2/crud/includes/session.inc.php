<?php
// Add this file to the top of every page that needs a logged in user.
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
