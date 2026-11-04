<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> - Message site of doom</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav class="navbar bg-dark mb-4" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Message site of doom</a>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <form class="d-flex align-items-center gap-3" action="logout.php" method="post">
                    <span class="navbar-text">Logged in as <?= h($_SESSION['username']) ?></span>
                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                    <button class="btn btn-outline-light btn-sm" type="submit">Log out</button>
                </form>
            <?php else: ?>
                <span class="navbar-text">Not logged in</span>
            <?php endif; ?>
        </div>
    </nav>
    <main class="container">
