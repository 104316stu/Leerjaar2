<?php require __DIR__ . '/partials/header.php'; ?>

<h1><?= h($pageTitle) ?></h1>
<div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?>" role="alert">
    <?= h($resultMessage) ?>
</div>
<a class="btn btn-primary" href="index.php">Back to overview</a>

<?php require __DIR__ . '/partials/footer.php'; ?>
