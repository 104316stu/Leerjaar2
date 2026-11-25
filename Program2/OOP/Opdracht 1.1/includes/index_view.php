<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>opdracht 1.1</title>
</head>
<body>
    <h1>getal conversie</h1>
    <form action="./index.php" method="post">
        <label for="getal">Getal:</label>
        <input type="text" id="getal" name="getal" placeholder="Voer een getal in">
        <hr>

        <button name="decbin">dec > bin</button> 
        <button name="bindec">bin > dec</button>
        <button name="dechex">dec > hex</button>
        <button name="hexdec">hex > dec</button>
        antwoord:
        <?php if ($antwoord !== "") { 
            ?> <p><?= $antwoord?> </p> <?php  
            } ?>
    </form>

</body>
</html>