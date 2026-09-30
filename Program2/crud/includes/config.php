<?php
$servername = 'localhost';
$username = '104316';
$localConfigFile = dirname(__DIR__, 3) . '/includes/config.local.php';
$localConfig = is_file($localConfigFile) ? (require $localConfigFile) : [];
$password = $localConfig['db_password'] ?? '';
$dbname = 'program_crud';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, $options);

    echo "Connected successfully";

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}