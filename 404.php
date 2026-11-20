<?php
require __DIR__ . '/includes/helpers.php';

http_response_code(404);
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

require __DIR__ . '/views/404_view.php';
