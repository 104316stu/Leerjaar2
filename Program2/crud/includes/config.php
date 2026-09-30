<?php
$envFile = dirname(__DIR__, 3) . '/.env';
$envExists = is_file($envFile);
$envReadable = is_readable($envFile);
$envPasswordFound = false;

if ($envReadable) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $value = trim($value, "\\\"'");

        if ($key !== '') {
            $_ENV[$key] = $value;
            $envPasswordFound = $key === 'Password' || $envPasswordFound;
        }
    }
}

$servername = "localhost";
$username = "104316";
$password = $_ENV['Password'] ?? '';
$dbname = "program_crud";
$passwordPreview = substr($password, 0, 2);
echo "ENV path: " . htmlspecialchars($envFile, ENT_QUOTES, 'UTF-8') . "<br>";
echo "ENV exists: " . ($envExists ? 'yes' : 'no') . "<br>";
echo "ENV readable: " . ($envReadable ? 'yes' : 'no') . "<br>";
echo "Password key found: " . ($envPasswordFound ? 'yes' : 'no') . "<br>";
echo "Password length: " . strlen($password) . "<br>";
echo "Password starts with: " . htmlspecialchars($passwordPreview, ENT_QUOTES, 'UTF-8') . "<br>";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, $options);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}