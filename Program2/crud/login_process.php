<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$errors = [];
$enteredUsername = clean_text($_POST, 'username');
$enteredPassword = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

if (!form_origin_is_valid()) {
    $errors[] = 'This form could not be verified. Try again.';
} elseif ($enteredUsername === '' || $enteredPassword === '') {
    $errors[] = 'Fill in your username and password.';
} else {
    try {
        $stmt = $conn->prepare('SELECT id, username, password_hash FROM users WHERE username = :username');
        $stmt->execute([':username' => $enteredUsername]);
        $user = $stmt->fetch();

        if ($user && password_verify($enteredPassword, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header('Location: index.php');
            exit;
        }

        $errors[] = 'Wrong username or password.';
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $errors[] = 'Logging in failed. Try again later.';
    }
}

$pageTitle = 'Login';
require __DIR__ . '/views/login_view.php';
