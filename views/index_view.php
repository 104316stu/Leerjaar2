<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="Schoolprojecten van leerjaar 2 op het GLR.">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:url" content="<?= e($pageUrl) ?>">
<?php if ($ogImage): ?>
<meta property="og:image" content="<?= e($ogImage) ?>">
<?php endif; ?>
<meta name="theme-color" content="#141414">
<link rel="icon" href="<?= e(asset('assets/favicon.svg')) ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Archivo:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('assets/style.css')) ?>">
<script src="<?= e(asset('assets/app.js')) ?>" defer></script>
</head>
<body>

<div class="bar">
  <?php if (!$isHome): ?>
  <nav class="crumbs" aria-label="Kruimelpad">
    <ol>
      <li><a href="./">Leerjaar 2</a></li>
      <?php foreach ($current['trail'] ?? [] as $key): ?>
        <li><a href="<?= e(layer_url($key)) ?>"><?= e($layers[$key]['title']) ?></a></li>
      <?php endforeach; ?>
      <?php if ($current): ?>
        <li><a href="<?= e(layer_url($current['key'])) ?>" aria-current="page"><?= e($current['title']) ?></a></li>
      <?php endif; ?>
    </ol>
  </nav>
  <?php endif; ?>
  <form class="search" role="search" action="./" data-search-form hidden>
    <label for="search" class="sr-only">Zoeken</label>
    <input id="search" name="q" type="search" placeholder="Zoeken" autocomplete="off" spellcheck="false">
  </form>
</div>

<main>
  <div id="view">
    <?php if ($isHome): ?>
      <?php require __DIR__ . '/partials/home.php'; ?>
    <?php elseif ($current): ?>
      <?php require __DIR__ . '/partials/layer.php'; ?>
    <?php else: ?>
      <h1 class="title">Niet gevonden</h1>
      <p><a href="./">Terug naar Leerjaar 2</a></p>
    <?php endif; ?>
  </div>
  <?php require __DIR__ . '/partials/search_results.php'; ?>
</main>

</body>
</html>
