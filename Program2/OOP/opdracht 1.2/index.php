<?php

include '../includes/functies.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lengte = isset($_POST['lengte']) ? (float)$_POST['lengte'] : null;
    $breedte = isset($_POST['breedte']) ? (float)$_POST['breedte'] : null;
    $hoogte = isset($_POST['hoogte']) && trim($_POST['hoogte']) !== ''
        ? (float)$_POST['hoogte']
        : null;

    $resultaat = berekenKamer($lengte, $breedte, $hoogte);
}

include './include/index_view.php';