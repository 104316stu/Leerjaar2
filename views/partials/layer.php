<?php $p = $current['project']; ?>
<header class="layer-head<?= $p && $p['cover'] ? ' has-cover' : '' ?>">
  <div>
    <h1 class="title"><?= e($current['title']) ?></h1>
    <?php if ($p): ?>
      <?php if ($p['description']): ?><p class="desc"><?= e($p['description']) ?></p><?php endif; ?>
      <?php if ($p['error']): ?><p class="err"><?= e($p['error']) ?></p><?php endif; ?>
      <?php if ($p['has_page'] || $p['github']): ?>
        <p class="actions">
          <?php if ($p['has_page'] && $p['link']): ?><a class="btn" href="<?= e($p['link']) ?>">Openen</a><?php endif; ?>
          <?php if ($p['github']): ?><a href="<?= e($p['github']) ?>">GitHub</a><?php endif; ?>
        </p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <?php if ($p && $p['cover']): ?>
    <img class="layer-cover" src="<?= e($p['cover']) ?>" alt="" width="960" height="600">
  <?php endif; ?>
</header>

<?php if ($current['folders']): ?>
  <section class="block" aria-label="Mappen">
    <?php $rows = $current['folders']; require __DIR__ . '/rows.php'; ?>
  </section>
<?php endif; ?>

<?php if ($current['projects']): ?>
  <section class="block" aria-label="Projecten">
    <ul class="cards">
      <?php foreach ($current['projects'] as $key): $layer = $layers[$key]; require __DIR__ . '/project.php'; endforeach; ?>
    </ul>
  </section>
<?php endif; ?>

<?php if ($current['pages']): ?>
  <section class="block" aria-label="Pagina's">
    <ul class="links">
      <?php foreach ($current['pages'] as $page): ?>
        <li><a href="<?= e($page['link']) ?>"><?= e($page['title']) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </section>
<?php endif; ?>

<?php if ($current['files']): ?>
  <section class="block" aria-label="Bestanden">
    <ul class="files">
      <?php foreach ($current['files'] as $f): ?>
        <li>
          <a class="file" href="<?= e($f['link']) ?>">
            <?php if ($f['image']): ?>
              <img src="<?= e($f['link']) ?>" alt="" loading="lazy">
            <?php else: ?>
              <span class="file-ext" aria-hidden="true"><?= e($f['ext']) ?></span>
            <?php endif; ?>
            <?= e($f['title']) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
<?php endif; ?>

<?php if ($p && $p['readme']): ?>
  <section class="block readme" aria-label="README"><?= readme_html($p['readme'], $p['folder']) ?></section>
<?php endif; ?>
