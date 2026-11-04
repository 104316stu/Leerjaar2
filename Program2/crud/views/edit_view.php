<?php require __DIR__ . '/partials/header.php'; ?>

<h1>Edit message</h1>
<?php require __DIR__ . '/partials/errors.php'; ?>

<?php
$formAction = 'edit_process.php';
$submitLabel = 'Save changes';
require __DIR__ . '/partials/message_form.php';
?>

<?php require __DIR__ . '/partials/footer.php'; ?>
