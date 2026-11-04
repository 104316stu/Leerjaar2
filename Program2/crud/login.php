<?php
session_start();
require_once __DIR__ . '/includes/helpers.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$enteredUsername = '';

$pageTitle = 'Login';
require __DIR__ . '/views/login_view.php';
