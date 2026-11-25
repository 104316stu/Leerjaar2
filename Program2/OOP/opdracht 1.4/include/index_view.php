<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>opdracht 1.4</title>
</head>
<body>
    <form action="./index.php" method="post">
        <label for="leeftijd">leeftijd</label>
        <input type="text" id="leeftijd" name="leeftijd" placeholder="Voer een getal in" required> <br>
        <input type="submit" value="submit">
    </form>
    <?php if (isset($resultaat)) { ?>
        <p><?= $resultaat ?></p>
    <?php } ?>
</body>
</html>