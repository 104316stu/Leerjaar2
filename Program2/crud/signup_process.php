<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$errors = [];
$enteredUsername = clean_text($_POST, 'username');
$enteredPassword = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

if (!form_origin_is_valid()) {
    $errors[] = 'This form could not be verified. Try again.';
} else {
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $enteredUsername)) {
        $errors[] = 'Your username must be 3 to 30 letters, numbers or underscores.';
    }
    if (strlen($enteredPassword) < 8) {
        $errors[] = 'Your password must be at least 8 characters.';
    }
}

if (empty($errors)) {
    try {
        $stmt = $conn->prepare('INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)');
        $stmt->execute([
            ':username' => $enteredUsername,
            ':password_hash' => password_hash($enteredPassword, PASSWORD_DEFAULT),
        ]);

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $conn->lastInsertId();
        $_SESSION['username'] = $enteredUsername;

        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        // 23000 means the unique username rule was broken.
        if ($e->getCode() === '23000') {
            $errors[] = 'This username is already taken.';
        } else {
            error_log($e->getMessage());
            $errors[] = 'Your account could not be created.';
        }
    }
}

$pageTitle = 'Sign up';
require __DIR__ . '/views/signup_view.php';
