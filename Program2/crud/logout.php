<?php
session_start();
require_once __DIR__ . '/includes/helpers.php';

if (!form_origin_is_valid()) {
    header('Location: index.php');
    exit;
}

$_SESSION = [];

$cookie = session_get_cookie_params();
setcookie(session_name(), '', time() - 3600, $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);

session_destroy();

header('Location: login.php');
exit;
