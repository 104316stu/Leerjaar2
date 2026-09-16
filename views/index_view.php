<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Leerjaar 2 · 104316</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Archivo:wght@400;500;600&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="top">
  <h1>Leerjaar 2<br><span>104316</span></h1>
</header>

<main>
<form class="filters" onsubmit="return false">
  <label for="search" class="filter-label">Zoek</label>
  <input id="search" type="search" placeholder="bijv. php, intro, crud" autocomplete="off" spellcheck="false">
  <div class="cats" role="group" aria-label="Map">
    <button type="button" class="cat is-active" data-cat="">Alle mappen</button>
    <?php foreach ($categories as $c => $n): ?>
      <button type="button" class="cat" data-cat="<?= e($c) ?>"><?= e($c) ?>/ <small><?= $n ?></small></button>
    <?php endforeach; ?>
  </div>
</form>

<div class="table-wrap">
<table class="listing">
  <thead>
    <tr>
      <th class="c-name">Project</th>
      <th class="c-path">Map</th>
      <th class="c-lang">Taal</th>
      <th class="c-push">Laatst bewerkt</th>
      <th class="c-src"></th>
    </tr>
  </thead>
  <tbody id="rows">
<?php foreach ($projects as $p): ?>
  <?php if ($p['type'] === 'github'):
      $r = $p['repo']['data'] ?? null;
      $hasReadme = !empty($p['readme']['data']);
      $searchText = strtolower(implode(' ', [$p['title'], $p['folder'], $r['description'] ?? '', $r['language'] ?? '', $p['github']['owner']]));
  ?>
    <tr class="row" data-cat="<?= e($p['category']) ?>" data-search="<?= e($searchText) ?>">
      <td class="c-name">
        <a class="name" href="<?= e($p['folder']) ?>/"><?= e($r['name'] ?? $p['title']) ?></a>
        <?php if ($r): ?><div class="desc"><?= e($r['description'] ?: '(geen omschrijving)') ?></div><?php endif; ?>
        <?php if ($hasReadme): ?>
          <button type="button" class="readme-toggle" aria-expanded="false">+ README</button>
        <?php endif; ?>
      </td>
      <td class="c-path"><?= e($p['folder']) ?>/</td>
      <td class="c-lang"><?= e($r['language'] ?? '–') ?></td>
      <td class="c-push" title="<?= e($r['pushed_at'] ?? '') ?>"><?= $r ? e(ago($r['pushed_at'])) : '–' ?></td>
      <td class="c-src"><a href="<?= e($p['folder']) ?>/">Openen</a></td>
    </tr>
    <?php if ($hasReadme): ?>
    <tr class="readme-row" data-cat="<?= e($p['category']) ?>" hidden>
      <td colspan="5"><div class="readme"><?= readme_html($p['readme']['data'], $p['folder']) ?></div></td>
    </tr>
    <?php endif; ?>
  <?php else: ?>
    <tr class="row" data-cat="<?= e($p['category']) ?>" data-search="<?= e(strtolower($p['title'] . ' ' . $p['folder'])) ?>">
      <td class="c-name"><a class="name" href="<?= e($p['link']) ?>"><?= e($p['title']) ?>/</a></td>
      <td class="c-path"><?= e($p['folder']) ?>/</td>
      <td class="c-lang">–</td>
      <td class="c-push">–</td>
      <td class="c-src"><a href="<?= e($p['link']) ?>">Openen</a></td>
    </tr>
  <?php endif; ?>
<?php endforeach; ?>
  </tbody>
</table>
</div>
<p class="empty" id="empty" hidden>Geen project gevonden. Probeer een ander woord of kies een andere map.</p>
</main>

<script src="assets/app.js" defer></script>
</body>
</html>
