<?php

include '../includes/functies.php';
$resultaat = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $resultaat = magStemmen((int)$_POST['leeftijd']);
        if (isset($resultaat)) {
            $resultaat = $resultaat ? "Ja, je mag stemmen." : "Nee, je mag niet stemmen.";
        } else {
            $resultaat = "Ongeldige invoer. Voer een geldig getal in.";
        }
    }

include './include/index_view.php';