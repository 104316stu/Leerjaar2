<?php
header('Content-Type: application/json');

$db = new SQLite3("album.db");
$db->busyTimeout(5000);

$query = "SELECT * FROM album ORDER BY albumId DESC LIMIT 25";
$result = $db->query($query);

$jsonArray = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $nummers = json_decode($row['albumNummers'] ?? '[]');
    if (!is_array($nummers)) {
        $nummers = [];
    }
    $row['albumNummers'] = $nummers;
    $jsonArray[] = $row;
}

$json = json_encode($jsonArray);

echo $json;
