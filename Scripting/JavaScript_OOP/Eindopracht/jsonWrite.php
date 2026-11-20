<?php
header('Content-Type: application/json');

$json = file_get_contents('php://input');

$data = json_decode($json);

$errors = [];

if (!$data) {
    $errors[] = 'Geen geldige JSON ontvangen.';
} else {
    if (!isset($data->albumTitel) || trim($data->albumTitel) == '') {
        $errors[] = 'Titel is verplicht.';
    }
    if (!isset($data->albumArtiest) || trim($data->albumArtiest) == '') {
        $errors[] = 'Artiest is verplicht.';
    }
    if (!isset($data->albumJaartal) || (int)$data->albumJaartal < 1900 || (int)$data->albumJaartal > date('Y')) {
        $errors[] = 'Jaartal moet tussen 1900 en ' . date('Y') . ' liggen.';
    }
    if (!isset($data->albumGenre) || trim($data->albumGenre) == '') {
        $errors[] = 'Genre is verplicht.';
    }
    if (!isset($data->albumNummers) || !is_array($data->albumNummers)) {
        $errors[] = 'Nummers moeten een lijst zijn.';
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit;
}

$db = new SQLite3("album.db");
$db->busyTimeout(5000);
$db->enableExceptions(true);

try {
    $stmt = $db->prepare("INSERT INTO album (albumTitel, albumArtiest, albumJaartal, albumGenre, albumNummers) VALUES (:titel, :artiest, :jaartal, :genre, :nummers)");
    $stmt->bindValue(':titel', trim($data->albumTitel), SQLITE3_TEXT);
    $stmt->bindValue(':artiest', trim($data->albumArtiest), SQLITE3_TEXT);
    $stmt->bindValue(':jaartal', (int)$data->albumJaartal, SQLITE3_INTEGER);
    $stmt->bindValue(':genre', trim($data->albumGenre), SQLITE3_TEXT);
    $stmt->bindValue(':nummers', json_encode($data->albumNummers), SQLITE3_TEXT);

    $stmt->execute();

    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['errors' => ['Het album kon niet worden opgeslagen.']]);
}
