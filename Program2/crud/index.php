<?php
require_once __DIR__ . '/includes/session.inc.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$messages = [];
$error = '';

try {
    $query = 'SELECT m.id, m.title, m.created_at, u.username FROM ' . MESSAGE_TABLE . ' m ';
    $query .= 'JOIN users u ON u.id = m.user_id ORDER BY m.created_at DESC, m.id DESC';

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'The messages could not be loaded.';
}

$pageTitle = 'Message Board';
require __DIR__ . '/views/index_view.php';
