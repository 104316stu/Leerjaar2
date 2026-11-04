<?php require __DIR__ . '/partials/header.php'; ?>

<h1>Login</h1>
<?php require __DIR__ . '/partials/errors.php'; ?>

<form action="login_process.php" method="post" class="form-narrow">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

    <div class="mb-3">
        <label class="form-label" for="username">Username</label>
        <input class="form-control" type="text" id="username" name="username" value="<?= h($enteredUsername) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label" for="password">Password</label>
        <input class="form-control" type="password" id="password" name="password" required>
    </div>

    <button class="btn btn-primary" type="submit">Login</button>
    <a class="btn btn-link" href="signup.php">Don't have an account? Sign up</a>
</form>

<?php require __DIR__ . '/partials/footer.php'; ?>
