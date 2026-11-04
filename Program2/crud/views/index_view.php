<?php require __DIR__ . '/partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Message Board</h1>
    <a class="btn btn-primary" href="add.php">New message</a>
</div>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger" role="alert"><?= h($error) ?></div>
<?php elseif (count($messages) === 0): ?>
    <p>There are no messages yet.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Poster</th>
                    <th>Posted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= h($message['title']) ?></td>
                        <td><?= h($message['username']) ?></td>
                        <td><?= h(format_datetime($message['created_at'])) ?></td>
                        <td class="text-nowrap">
                            <a class="btn btn-outline-secondary btn-sm" href="detail.php?id=<?= h($message['id']) ?>">Details</a>
                            <a class="btn btn-outline-primary btn-sm" href="edit.php?id=<?= h($message['id']) ?>">Edit</a>
                            <a class="btn btn-outline-danger btn-sm" href="delete.php?id=<?= h($message['id']) ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
