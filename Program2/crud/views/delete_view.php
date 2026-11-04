<?php require __DIR__ . '/partials/header.php'; ?>

<h1>Delete message</h1>
<p>Are you sure you want to delete this message?</p>

<div class="card mb-3">
    <div class="card-body">
        <h2 class="h5"><?= h($message['title']) ?></h2>
        <p class="message-text"><?= h($message['message']) ?></p>
        <p class="text-secondary mb-0">
            By <?= h($message['username']) ?> on <?= h(format_datetime($message['created_at'])) ?>
        </p>
    </div>
</div>

<form action="delete_process.php" method="post">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= h($message['id']) ?>">
    <button class="btn btn-danger" type="submit">Yes, delete it</button>
    <a class="btn btn-link" href="index.php">No, go back</a>
</form>

<?php require __DIR__ . '/partials/footer.php'; ?>
