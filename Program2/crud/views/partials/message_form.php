<form action="<?= h($formAction) ?>" method="post" class="form-narrow">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <?php if (!empty($message['id'])): ?>
        <input type="hidden" name="id" value="<?= h($message['id']) ?>">
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label" for="title">Title</label>
        <input class="form-control" type="text" id="title" name="title" maxlength="50" value="<?= h($message['title']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label" for="message">Message</label>
        <textarea class="form-control" id="message" name="message" rows="5" maxlength="2000" required><?= h($message['message']) ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label" for="category">Category</label>
        <select class="form-select" id="category" name="category" required>
            <?php foreach (MESSAGE_CATEGORIES as $value => $label): ?>
                <option value="<?= h($value) ?>" <?= $message['category'] === $value ? 'selected' : '' ?>><?= h($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label" for="priority">Priority (1 to 5)</label>
        <input class="form-control" type="number" id="priority" name="priority" min="1" max="5" value="<?= h($message['priority']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label" for="event_date">Date</label>
        <input class="form-control" type="date" id="event_date" name="event_date" value="<?= h($message['event_date']) ?>" required>
    </div>

    <button class="btn btn-primary" type="submit"><?= h($submitLabel) ?></button>
    <a class="btn btn-link" href="index.php">Back to overview</a>
</form>
