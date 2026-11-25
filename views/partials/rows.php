<?php /* A list of vakken or folders. Expects $rows (layer keys). */ ?>
<ul class="rows">
  <?php foreach ($rows as $key): $inside = layer_contents($layers, $layers[$key]); ?>
    <li>
      <a class="row" href="<?= e(layer_url($key)) ?>">
        <strong><?= e($layers[$key]['title']) ?></strong>
        <span><?= e(implode(', ', array_slice($inside, 0, 4))) ?><?php if (count($inside) > 4): ?>, …<?php endif; ?></span>
      </a>
    </li>
  <?php endforeach; ?>
</ul>
