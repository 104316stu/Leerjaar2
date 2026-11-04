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

try {
    $stmt = $conn->prepare('DELETE FROM ' . MESSAGE_TABLE . ' WHERE id = :id');
    $stmt->execute([':id' => $id]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    show_result(false, 'The message could not be deleted.', 500);
}

if ($stmt->rowCount() === 0) {
    show_result(false, 'This message does not exist.', 404);
}

show_result(true, 'The message has been deleted.');
