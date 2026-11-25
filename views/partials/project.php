<?php
/* One project as a card. Expects $layer. */
$p       = $layer['project'];
$details = $p['has_page'] && ($p['readme'] || $layer['pages']);
?>
<li class="card">
  <a class="card-cover" href="<?= e(project_href($layer)) ?>" tabindex="-1" aria-hidden="true">
    <?php if ($p['cover']): ?>
      <img src="<?= e($p['cover']) ?>" alt="" loading="lazy" width="640" height="400">
    <?php else: ?>
      <span><?= e($p['title']) ?></span>
    <?php endif; ?>
  </a>
  <h2><a href="<?= e(project_href($layer)) ?>"><?= e($p['title']) ?></a></h2>
  <?php if ($p['description']): ?><p class="desc"><?= e($p['description']) ?></p><?php endif; ?>
  <?php if ($p['error']): ?><p class="err"><?= e($p['error']) ?></p><?php endif; ?>
  <?php if ($details || $p['github']): ?>
    <p class="card-links">
      <?php if ($details): ?><a href="<?= e(layer_url($layer['key'])) ?>"><?= $p['readme'] ? 'README' : 'Onderdelen' ?></a><?php endif; ?>
      <?php if ($p['github']): ?><a href="<?= e($p['github']) ?>">GitHub</a><?php endif; ?>
    </p>
  <?php endif; ?>
</li>
