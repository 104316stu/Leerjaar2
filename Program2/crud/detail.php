<?php
require_once __DIR__ . '/includes/session.inc.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$id = read_id($_GET['id'] ?? '');
if ($id === false) {
    show_result(false, 'No valid message was chosen.', 400);
}

try {
    $message = find_message($conn, $id);
} catch (PDOException $e) {
    error_log($e->getMessage());
    show_result(false, 'The message could not be loaded.', 500);
}

if (!$message) {
    show_result(false, 'This message does not exist.', 404);
}

$pageTitle = $message['title'];
require __DIR__ . '/views/detail_view.php';
