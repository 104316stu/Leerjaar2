<?php
session_start();
require_once __DIR__ . '/includes/helpers.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$enteredUsername = '';

$pageTitle = 'Sign up';
require __DIR__ . '/views/signup_view.php';
