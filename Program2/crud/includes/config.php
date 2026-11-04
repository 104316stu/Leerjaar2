<?php
// Show errors while developing. Remove these two lines when the app is finished.
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = 'localhost';
$username = '104316';
$localConfigFile = dirname(__DIR__, 3) . '/includes/config.local.php';
$localConfig = is_file($localConfigFile) ? (require $localConfigFile) : [];
$password = $localConfig['db_password'] ?? '';
$dbname = 'program_crud';

// The table name is set here once, so it only has to be changed in this file.
const MESSAGE_TABLE = 'messages';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    exit('Connection failed: ' . $e->getMessage());
}
