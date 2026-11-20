<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Niet gevonden · Leerjaar 2</title>
<meta name="robots" content="noindex">
<link rel="icon" href="/<?= e(asset('assets/favicon.svg')) ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Archivo:wght@400;500;600&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/<?= e(asset('assets/style.css')) ?>">
</head>
<body>
<main class="notfound">
  <p class="kicker">Fout 404</p>
  <h1>Niet <span>hier</span></h1>
  <p>Op <code><?= e($path) ?></code> staat niets. Misschien is het project verplaatst of heeft het een andere naam gekregen.</p>
  <p><a class="btn" href="/">Naar alle projecten <span aria-hidden="true">→</span></a></p>
</main>
</body>
</html>
