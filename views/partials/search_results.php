<section id="results" class="block" aria-label="Zoekresultaten" hidden>
  <p id="no-results" role="status" hidden>Niets gevonden.</p>
  <ul class="results">
    <?php /* data-shot and data-url are read by .github/scripts/covers.py */ ?>
    <?php foreach ($searchItems as $item): $p = $item['project']; ?>
      <li data-search="<?= e($item['search']) ?>"<?php if ($p && $p['needs_shot']): ?> data-shot="<?= e($p['slug']) ?>" data-url="<?= e($p['link']) ?>"<?= $p['auto_cover'] ? ' data-has-shot' : '' ?><?php endif; ?> hidden>
        <a href="<?= e($item['href']) ?>"><strong><?= e($item['title']) ?></strong> <span><?= e($item['path']) ?></span></a>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
