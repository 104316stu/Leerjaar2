<?php
require_once __DIR__ . '/includes/session.inc.php';
require_once __DIR__ . '/includes/helpers.php';

$message = [
    'title' => '',
    'message' => '',
    'category' => 'general',
    'priority' => '3',
    'event_date' => date('Y-m-d'),
];
$errors = [];

$pageTitle = 'New message';
require __DIR__ . '/views/add_view.php';
