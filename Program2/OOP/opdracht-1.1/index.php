<?php
$antwoord = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $getal = $_POST['getal'];

    if (isset($_POST['decbin'])) {
        $antwoord = decbin($getal);
    } elseif (isset($_POST['bindec'])) {
        $antwoord = bindec($getal);
    } elseif (isset($_POST['dechex'])) {
        $antwoord = dechex($getal);
    } elseif (isset($_POST['hexdec'])) {
        $antwoord = hexdec($getal);
    }
}


include 'includes/index_view.php';