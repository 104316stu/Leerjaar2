<?php
const MESSAGE_CATEGORIES = [
    'general' => 'General',
    'question' => 'Question',
    'idea' => 'Idea',
];

// Makes a value safe to show in HTML.
function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function format_datetime($value)
{
    return date('d-m-Y H:i', strtotime($value));
}

function format_date($value)
{
    return date('d-m-Y', strtotime($value));
}

// Creates the random token that is stored in the session and in every form.
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Checks that the request is a POST from a form on this website.
function form_origin_is_valid()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    }

    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $formToken = $_POST['csrf_token'] ?? '';
    if ($sessionToken === '' || !is_string($formToken) || !hash_equals($sessionToken, $formToken)) {
        return false;
    }

    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if ($referer !== '') {
        $serverHost = explode(':', $_SERVER['HTTP_HOST'])[0];
        return parse_url($referer, PHP_URL_HOST) === $serverHost;
    }

    return true;
}

// Reads a text field from the form and cleans it.
function clean_text($input, $name)
{
    $value = $input[$name] ?? '';

    return is_string($value) ? trim(strip_tags($value)) : '';
}

function read_id($value)
{
    return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
}

function is_valid_date($value)
{
    $date = DateTime::createFromFormat('Y-m-d', $value);

    return $date !== false && $date->format('Y-m-d') === $value;
}

// Cleans and checks the message form. Used by both the add and the edit page.
function validate_message($input)
{
    $message = [
        'title' => clean_text($input, 'title'),
        'message' => clean_text($input, 'message'),
        'category' => clean_text($input, 'category'),
        'priority' => clean_text($input, 'priority'),
        'event_date' => clean_text($input, 'event_date'),
    ];
    $errors = [];

    if ($message['title'] === '') {
        $errors[] = 'Fill in a title.';
    } elseif (mb_strlen($message['title']) > 50) {
        $errors[] = 'The title can be 50 characters at most.';
    }

    if ($message['message'] === '') {
        $errors[] = 'Fill in a message.';
    } elseif (mb_strlen($message['message']) > 2000) {
        $errors[] = 'The message can be 2000 characters at most.';
    }

    if (!array_key_exists($message['category'], MESSAGE_CATEGORIES)) {
        $errors[] = 'Choose a valid category.';
    }

    $priority = filter_var($message['priority'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 5],
    ]);
    if ($priority === false) {
        $errors[] = 'The priority must be a number from 1 to 5.';
    }

    if (!is_valid_date($message['event_date'])) {
        $errors[] = 'Fill in a valid date.';
    }

    return [$message, $errors];
}

// Gets one message with the name of its poster, or false if it does not exist.
function find_message($conn, $id)
{
    $query = 'SELECT m.*, u.username FROM ' . MESSAGE_TABLE . ' m ';
    $query .= 'JOIN users u ON u.id = m.user_id WHERE m.id = :id';

    $stmt = $conn->prepare($query);
    $stmt->execute([':id' => $id]);

    return $stmt->fetch();
}

// Shows the result page with a message and a link back, then stops the script.
function show_result($success, $resultMessage, $statusCode = 200)
{
    http_response_code($statusCode);
    $pageTitle = $success ? 'Done' : 'Something went wrong';
    require __DIR__ . '/../views/result_view.php';
    exit;
}
