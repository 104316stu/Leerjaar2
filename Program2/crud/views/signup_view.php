<?php require __DIR__ . '/partials/header.php'; ?>

<h1>Sign up</h1>
<?php require __DIR__ . '/partials/errors.php'; ?>

<form action="signup_process.php" method="post" class="form-narrow">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

    <div class="mb-3">
        <label class="form-label" for="username">Username</label>
        <input class="form-control" type="text" id="username" name="username" minlength="3" maxlength="30" pattern="[A-Za-z0-9_]+" value="<?= h($enteredUsername) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label" for="password">Password (at least 8 characters)</label>
        <input class="form-control" type="password" id="password" name="password" minlength="8" required>
    </div>

    <button class="btn btn-primary" type="submit">Sign up</button>
    <a class="btn btn-link" href="login.php">Already have an account? Login</a>
</form>

<?php require __DIR__ . '/partials/footer.php'; ?>
