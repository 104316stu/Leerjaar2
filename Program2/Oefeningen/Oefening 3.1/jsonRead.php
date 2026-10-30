<?php
// Zet het content type wat de pagina stuurt op JSON.
header('Content-Type: application/json');

// Maak verbinding met de database
$db = new SQLite3("student.db");
$db->busyTimeout(5000);
 
// Selecter maximaal 10 studenten uit de student tabel
// gesorteerd op studentNaam aflopend.
$query = "SELECT * FROM student ORDER BY rowid DESC LIMIT 25";
$result=$db->query($query);

// Doorloop de resultaten en plaats deze in een Array
// We gebruiken in het bijzonder de SQLITE3_ASSOC - Associative Array 
// zodat we de namen van de key en de waarde van de values mooi in 
// onze JSON file krijgen.
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $jsonArray[] = $row;
}

// Encodeer de Array en maak er een JSON encoded string van
$json = json_encode($jsonArray);

// Toon de JSON string op het scherm
echo $json;

