<?php
session_start();

if (empty($_SESSION['logged_in'])) {
	require_once __DIR__ . '/views/login_view.php';
} else {
	require_once __DIR__ . '/views/index_view.php';
}
