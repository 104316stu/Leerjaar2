<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>opdracht 1.2</title>
</head>
<body>
    <form action="./index.php" method="post">
        <label for="lengte">lengte</label>
        <input type="text" id="lengte" name="lengte" placeholder="Voer een getal in" required> <br>
        <label for="breedte">breedte</label>
        <input type="text" id="breedte" name="breedte" placeholder="Voer een getal in" required> <br>
        <label for="hoogte">hoogte</label>
        <input type="text" id="hoogte" name="hoogte" placeholder="Voer een getal in"> <br>
        <input type="submit" value="Bereken">
    </form>
    <?php if (isset($resultaat)) { ?>
        <p>Het resultaat is: <?= $resultaat ?></p>
    <?php } ?>
</body>
</html>