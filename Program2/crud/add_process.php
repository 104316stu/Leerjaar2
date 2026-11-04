<?php
require_once __DIR__ . '/includes/session.inc.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

if (!form_origin_is_valid()) {
    show_result(false, 'This form could not be verified. Open the form and try again.', 400);
}

[$message, $errors] = validate_message($_POST);

if (empty($errors)) {
    try {
        $query = 'INSERT INTO ' . MESSAGE_TABLE . ' ';
        $query .= '(user_id, title, message, category, priority, event_date) ';
        $query .= 'VALUES (:user_id, :title, :message, :category, :priority, :event_date)';

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':title' => $message['title'],
            ':message' => $message['message'],
            ':category' => $message['category'],
            ':priority' => $message['priority'],
            ':event_date' => $message['event_date'],
        ]);

        show_result(true, 'Your message has been posted.');
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $errors[] = 'The message could not be saved.';
    }
}

$pageTitle = 'New message';
require __DIR__ . '/views/add_view.php';
