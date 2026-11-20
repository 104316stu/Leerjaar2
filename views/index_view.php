<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Leerjaar 2 · 104316</title>
<meta name="description" content="Schoolprojecten van leerjaar 2 op het GLR, per vak, met een live link naar elk project.">
<meta property="og:type" content="website">
<meta property="og:title" content="Leerjaar 2 · 104316">
<meta property="og:description" content="Schoolprojecten van leerjaar 2 op het GLR, per vak.">
<meta property="og:url" content="<?= e($siteUrl) ?>">
<?php if ($ogImage): ?>
<meta property="og:image" content="<?= e($ogImage) ?>">
<?php endif; ?>
<meta name="theme-color" content="#141414">
<link rel="icon" href="<?= e(asset('assets/favicon.svg')) ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Archivo:wght@400;500;600&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('assets/style.css')) ?>">
<script src="<?= e(asset('assets/app.js')) ?>" defer></script>
</head>
<body>

<header class="masthead">
  <p class="kicker">Software developer · GLR</p>
  <h1>Leerjaar 2<br><span>104316</span></h1>
  <p class="intro">Alles wat ik dit jaar maak, per vak. Elk project opent live, zoals het nu op de server staat.</p>
  <dl class="stats">
    <div><dt>Projecten</dt><dd><?= count($projects) ?></dd></div>
    <div><dt>Vakken</dt><dd><?= count($groups) ?></dd></div>
    <?php if ($lastUpdate): ?>
      <div><dt>Laatste update</dt><dd><time datetime="<?= e($lastUpdate) ?>"><?= e(ago($lastUpdate)) ?></time></dd></div>
    <?php endif; ?>
  </dl>
</header>

<main>
<?php if ($featured): $p = $featured; ?>
  <section class="featured<?= $p['cover'] ? ' has-cover' : '' ?>" aria-labelledby="featured-title">
    <?php if ($p['cover']): ?>
      <a class="featured-cover" href="<?= e($p['link'] ?? '#') ?>" tabindex="-1" aria-hidden="true">
        <img src="<?= e($p['cover']) ?>" alt="" width="1280" height="800">
      </a>
    <?php endif; ?>
    <div class="featured-body">
      <p class="label">Uitgelicht</p>
      <h2 id="featured-title">
        <?php if ($p['link']): ?><a href="<?= e($p['link']) ?>"><?= e($p['title']) ?></a><?php else: ?><?= e($p['title']) ?><?php endif; ?>
      </h2>
      <?php if ($p['description']): ?><p class="desc"><?= e($p['description']) ?></p><?php endif; ?>
      <?php if ($p['tags']): ?>
        <ul class="tags"><?php foreach ($p['tags'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul>
      <?php endif; ?>
      <p class="actions">
        <?php if ($p['link']): ?><a class="btn" href="<?= e($p['link']) ?>">Project openen <span aria-hidden="true">→</span></a><?php endif; ?>
        <?php if ($p['github']): ?><a href="<?= e($p['github']) ?>">Code op GitHub</a><?php endif; ?>
      </p>
      <?php if ($p['updated']): ?><p class="meta">Bijgewerkt <?= e(ago($p['updated'])) ?></p><?php endif; ?>
    </div>
  </section>
<?php endif; ?>

<form class="filters" role="search" action="#" data-filters>
  <label for="search">Zoek</label>
  <input id="search" type="search" placeholder="Naam, taal of map" autocomplete="off" spellcheck="false">
  <div class="cats" role="group" aria-label="Vak">
    <button type="button" class="cat is-active" data-cat="" aria-pressed="true">Alles <small><?= count($projects) ?></small></button>
    <?php foreach ($groups as $g): ?>
      <button type="button" class="cat" data-cat="<?= e($g['key']) ?>" aria-pressed="false"><?= e($g['name']) ?> <small><?= count($g['projects']) ?></small></button>
    <?php endforeach; ?>
  </div>
</form>

<?php foreach ($groups as $g): ?>
  <section class="vak" data-vak="<?= e($g['key']) ?>" id="vak-<?= e(strtolower($g['key'])) ?>" aria-labelledby="h-<?= e(strtolower($g['key'])) ?>">
    <header class="vak-head">
      <span class="vak-nr" aria-hidden="true"><?= e($g['nr']) ?></span>
      <h2 id="h-<?= e(strtolower($g['key'])) ?>"><?= e($g['name']) ?></h2>
      <span class="vak-count"><?= count($g['projects']) ?> <?= count($g['projects']) === 1 ? 'project' : 'projecten' ?></span>
    </header>
    <ol class="projects">
      <?php foreach ($g['projects'] as $p): ?>
        <?php require __DIR__ . '/partials/project.php'; ?>
      <?php endforeach; ?>
    </ol>
  </section>
<?php endforeach; ?>

<p class="empty" data-empty hidden>Geen project gevonden. Probeer een ander woord of kies een ander vak.</p>
</main>

<footer class="site-foot">
  <p>
    <?php if ($lastUpdate): ?>
      Laatste deploy <time datetime="<?= e($lastUpdate) ?>"><?= e(ago($lastUpdate)) ?></time><?php if ($buildSha): ?> · <code><?= e($buildSha) ?></code><?php endif; ?>
    <?php else: ?>
      Nog geen deploy-informatie.
    <?php endif; ?>
  </p>
  <p><a href="#">Naar boven</a></p>
</footer>

</body>
</html>
