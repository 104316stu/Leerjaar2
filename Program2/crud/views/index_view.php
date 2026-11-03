<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message site of doom - home</title>
</head>
<body>
    <form action="index_view.php" method="post">
        <button type="submit" name="logout" value="1">Log out</button>
    </form>

    <div class="Message-Board">
        <h1>Message Board</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <p>Write a new message?</p>
        <form action="post_message.php" method="post">
            <textarea name="message" rows="4" cols="50" placeholder="Write your message here..." required></textarea><br>
            <button type="submit">Post Message</button>
        </form>

    </div>

    <div>
        <p>Messages will appear here.</p>
        <div>
            <hr>
            <p id="poster">John</p>
            <p id="message">sadasdasdasdsad asdad ada s</p>
            <p id="timestamp">2023-10-01 12:00:00</p>
            <hr>
        </div>

</div>
</body>
</html>