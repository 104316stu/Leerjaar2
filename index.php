<?php
require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/projects.php';
require __DIR__ . '/includes/deploy.php';

$root     = __DIR__;
$build    = load_build($root);
$projects = load_projects($root, $build);
$groups   = group_by_subject($projects);

$featured = array_values(array_filter($projects, fn ($p) => $p['featured']));
usort($featured, fn ($a, $b) => ($b['updated'] ?? 0) <=> ($a['updated'] ?? 0));
$featured = $featured[0] ?? null;

$lastUpdate = $build['time'] ?? null;
$buildSha   = isset($build['sha']) ? substr($build['sha'], 0, 7) : null;

// Full address of the site, for link previews (og:image needs an absolute URL).
$siteUrl  = (($_SERVER['HTTPS'] ?? '') === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/';
$ogImage  = $featured && $featured['cover'] && !preg_match('~^https?://~', $featured['cover'])
    ? $siteUrl . $featured['cover']
    : ($featured['cover'] ?? null);

require __DIR__ . '/views/index_view.php';

// Send the page to the visitor first, then check GitHub, so nobody waits on the API.
// deploy-check.php does the same on a timer; this is the fallback when that is not set up.
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
    deploy_check($root);
}
