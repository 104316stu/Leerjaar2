<?php require __DIR__ . '/partials/header.php'; ?>

<h1><?= h($message['title']) ?></h1>
<p class="message-text"><?= h($message['message']) ?></p>

<dl class="row">
    <dt class="col-sm-3">Poster</dt>
    <dd class="col-sm-9"><?= h($message['username']) ?></dd>
    <dt class="col-sm-3">Category</dt>
    <dd class="col-sm-9"><?= h(MESSAGE_CATEGORIES[$message['category']]) ?></dd>
    <dt class="col-sm-3">Priority</dt>
    <dd class="col-sm-9"><?= h($message['priority']) ?></dd>
    <dt class="col-sm-3">Date</dt>
    <dd class="col-sm-9"><?= h(format_date($message['event_date'])) ?></dd>
    <dt class="col-sm-3">Posted</dt>
    <dd class="col-sm-9"><?= h(format_datetime($message['created_at'])) ?></dd>
</dl>

<a class="btn btn-outline-primary" href="edit.php?id=<?= h($message['id']) ?>">Edit</a>
<a class="btn btn-outline-danger" href="delete.php?id=<?= h($message['id']) ?>">Delete</a>
<a class="btn btn-link" href="index.php">Back to overview</a>

<?php require __DIR__ . '/partials/footer.php'; ?>
