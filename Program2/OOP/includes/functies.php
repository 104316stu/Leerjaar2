<?php


function berekenKamer (float $lengte, float $breedte, ?float $hoogte = null): float {
    $oppervlakte = $lengte * $breedte;

    if ($hoogte === null) {
        return $oppervlakte;
    }

    return $oppervlakte * $hoogte;
}


function nlDatum(string $datum, bool $kortJaar = false): string {
    $parsedDatum = new DateTimeImmutable($datum);
    $maanden = [
        1 => 'januari',
        2 => 'februari',
        3 => 'maart',
        4 => 'april',
        5 => 'mei',
        6 => 'juni',
        7 => 'juli',
        8 => 'augustus',
        9 => 'september',
        10 => 'oktober',
        11 => 'november',
        12 => 'december',
    ];

    $dag   = $parsedDatum->format('j');
    $maand = $maanden[(int) $parsedDatum->format('n')];
    $jaar  = $kortJaar ? '’' . $parsedDatum->format('y') : $parsedDatum->format('Y');

    return $dag . ' ' . $maand . ' ' . $jaar;
}




function magStemmen(int $leeftijd): bool {
    if ($leeftijd >= 18) {
        return true;
    } else {
        return false;
    }
}