<li class="project<?= $p['cover'] ? ' has-cover' : '' ?>"
    data-vak="<?= e($p['subject']) ?>"
    data-search="<?= e($p['search']) ?>"
    <?php if ($p['needs_shot']): ?>data-shot="<?= e($p['slug']) ?>" data-url="<?= e($p['link']) ?>"<?= $p['auto_cover'] ? ' data-has-shot' : '' ?><?php endif; ?>>
  <?php if ($p['cover']): ?>
    <a class="thumb" href="<?= e($p['link'] ?? $p['cover']) ?>" tabindex="-1" aria-hidden="true">
      <img src="<?= e($p['cover']) ?>" alt="" loading="lazy" width="640" height="400">
    </a>
  <?php endif; ?>

  <div class="p-main">
    <h3>
      <?php if ($p['link']): ?><a href="<?= e($p['link']) ?>"><?= e($p['title']) ?></a><?php else: ?><?= e($p['title']) ?><?php endif; ?>
      <?php if ($p['is_new']): ?><span class="badge">nieuw</span><?php endif; ?>
      <?php if ($p['status']): ?><span class="status"><?= e($p['status']) ?></span><?php endif; ?>
    </h3>
    <?php if ($p['description']): ?><p class="desc"><?= e($p['description']) ?></p><?php endif; ?>
    <?php if ($p['error']): ?><p class="err"><?= e($p['error']) ?></p><?php endif; ?>

    <?php if ($p['parts']): ?>
      <ul class="parts" aria-label="Onderdelen">
        <?php foreach ($p['parts'] as $part): ?><li><a href="<?= e($part['link']) ?>"><?= e($part['title']) ?></a></li><?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <?php if ($p['tags']): ?>
      <ul class="tags"><?php foreach ($p['tags'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul>
    <?php endif; ?>

    <?php if ($p['readme']): ?>
      <details class="readme">
        <summary>README</summary>
        <div class="readme-body"><?= readme_html($p['readme'], $p['folder']) ?></div>
      </details>
    <?php endif; ?>
  </div>

  <dl class="p-meta">
    <div><dt>Map</dt><dd><?= implode('/<wbr>', array_map('e', explode('/', $p['folder']))) ?>/</dd></div>
    <?php if ($p['language']): ?><div><dt>Taal</dt><dd><?= e($p['language']) ?></dd></div><?php endif; ?>
    <?php if ($p['period']): ?><div><dt>Periode</dt><dd><?= e($p['period']) ?></dd></div><?php endif; ?>
    <?php if ($p['updated']): ?><div><dt>Bijgewerkt</dt><dd><time datetime="<?= e(date('c', $p['updated'])) ?>"><?= e(ago($p['updated'])) ?></time></dd></div><?php endif; ?>
  </dl>

  <p class="p-actions">
    <?php if ($p['link']): ?><a class="open" href="<?= e($p['link']) ?>">Openen <span aria-hidden="true">→</span></a><?php endif; ?>
    <?php if ($p['github']): ?><a href="<?= e($p['github']) ?>">GitHub</a><?php endif; ?>
  </p>
</li>
