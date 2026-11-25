<?php
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/layers.php';
require __DIR__ . '/includes/deploy.php';

$root     = __DIR__;
$projects = load_projects($root, load_build($root));
$groups   = group_by_subject($projects);
$layers   = build_layers($root, $groups);

// ?vak=Program2/OOP. Only looked up in $layers, so it never reaches the filesystem.
$layerKey = is_string($_GET['vak'] ?? null) ? trim($_GET['vak'], '/') : '';
$isHome   = $layerKey === '';
$current  = $layers[$layerKey] ?? null;
if (!$isHome && !$current) {
    http_response_code(404);
}

$searchItems = search_index($layers);

$pageTitle = match (true) {
    $isHome          => 'Leerjaar 2 · 104316',
    $current !== null => $current['title'] . ' · Leerjaar 2',
    default          => 'Niet gevonden · Leerjaar 2',
};

// Full addresses for link previews. The image is the project's cover, or that of the
// newest project with "uitgelicht" in its project.json.
$featured = array_values(array_filter($projects, fn ($p) => $p['featured']));
usort($featured, fn ($a, $b) => ($b['updated'] ?? 0) <=> ($a['updated'] ?? 0));
$siteUrl = (($_SERVER['HTTPS'] ?? '') === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/';
$pageUrl = $siteUrl . ($current ? substr(layer_url($layerKey), 2) : '');
$ogImage = $current['project']['cover'] ?? $featured[0]['cover'] ?? null;
if ($ogImage && !preg_match('~^https?://~', $ogImage)) {
    $ogImage = $siteUrl . $ogImage;
}

require __DIR__ . '/views/index_view.php';

// Send the page to the visitor first, then check GitHub, so nobody waits on the API.
// deploy-check.php does the same on a timer; this is the fallback when that is not set up.
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
    deploy_check($root);
}
