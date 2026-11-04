<?php
require_once __DIR__ . '/includes/session.inc.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

if (!form_origin_is_valid()) {
    show_result(false, 'This form could not be verified. Open the form and try again.', 400);
}

$id = read_id($_POST['id'] ?? '');
if ($id === false) {
    show_result(false, 'No valid message was chosen.', 400);
}

[$message, $errors] = validate_message($_POST);
$message['id'] = $id;

if (empty($errors)) {
    try {
        if (!find_message($conn, $id)) {
            show_result(false, 'This message does not exist.', 404);
        }

        $query = 'UPDATE ' . MESSAGE_TABLE . ' SET title = :title, message = :message, ';
        $query .= 'category = :category, priority = :priority, event_date = :event_date ';
        $query .= 'WHERE id = :id';

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':title' => $message['title'],
            ':message' => $message['message'],
            ':category' => $message['category'],
            ':priority' => $message['priority'],
            ':event_date' => $message['event_date'],
            ':id' => $id,
        ]);

        show_result(true, 'The message has been updated.');
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $errors[] = 'The message could not be saved.';
    }
}

$pageTitle = 'Edit message';
require __DIR__ . '/views/edit_view.php';
