<?php require __DIR__ . '/partials/header.php'; ?>

<h1>New message</h1>
<?php require __DIR__ . '/partials/errors.php'; ?>

<?php
$formAction = 'add_process.php';
$submitLabel = 'Post message';
require __DIR__ . '/partials/message_form.php';
?>

<?php require __DIR__ . '/partials/footer.php'; ?>
