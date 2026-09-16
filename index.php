<?php
require __DIR__ . '/includes/github.php';

/* ---------------------------------------------------------------
 * Scan the web root for projects.
 *  - a folder containing a *.git file   -> GitHub repository
 *  - a folder containing index.php/html -> local project
 * ------------------------------------------------------------- */
$root     = __DIR__;
$skip     = ['.git', '.github', 'cache', 'includes', 'assets', 'views', 'node_modules', 'vendor'];
$projects = [];

$it = new RecursiveIteratorIterator(
    new RecursiveCallbackFilterIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
        fn ($f) => !($f->isDir() && in_array($f->getFilename(), $skip, true))
    ),
    RecursiveIteratorIterator::SELF_FIRST
);
$it->setMaxDepth(3);

foreach ($it as $file) {
    if ($file->isDir()) {
        continue;
    }
    $rel  = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
    $dir  = dirname($rel);
    $name = $file->getFilename();

    if (str_ends_with($name, '.git')) {
        $gh = github_parse_git_file($file->getPathname());
        if ($gh) {
            $projects[] = [
                'type'     => 'github',
                'title'    => basename($name, '.git'),
                'folder'   => $dir,
                'category' => explode('/', $rel)[0],
                'github'   => $gh,
            ];
        }
    } elseif ($dir !== '.' && in_array($name, ['index.php', 'index.html'], true)) {
        $projects[] = [
            'type'     => 'local',
            'title'    => basename($dir),
            'folder'   => $dir,
            'category' => explode('/', $rel)[0],
            'link'     => $dir . '/',
        ];
    }
}

// A folder with both a *.git file and an index file is one project, not two:
// keep the GitHub entry (it carries the description etc.), drop the local one.
$githubFolders = array_column(array_filter($projects, fn ($p) => $p['type'] === 'github'), 'folder');
$projects = array_values(array_filter(
    $projects,
    fn ($p) => $p['type'] === 'github' || !in_array($p['folder'], $githubFolders, true)
));

// Fetch GitHub data (served from cache unless GitHub reports changes).
foreach ($projects as &$p) {
    if ($p['type'] !== 'github') {
        continue;
    }
    $p['repo']   = github_repo($p['github']['owner'], $p['github']['repo']);
    $p['readme'] = github_readme($p['github']['owner'], $p['github']['repo']);
}
unset($p);

usort($projects, fn ($a, $b) => [$a['category'], $a['title']] <=> [$b['category'], $b['title']]);

// Category => number of projects, for the sidebar.
$categories = [];
foreach ($projects as $p) {
    $categories[$p['category']] = ($categories[$p['category']] ?? 0) + 1;
}

$githubCount = count(array_filter($projects, fn ($p) => $p['type'] === 'github'));
$localCount  = count($projects) - $githubCount;

/* ---------------------------------------------------------------
 * View helpers
 * ------------------------------------------------------------- */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function ago(?string $iso): string
{
    if (!$iso) {
        return '';
    }
    $d = time() - strtotime($iso);
    foreach ([31536000 => 'jaar', 2592000 => 'maand', 604800 => 'week', 86400 => 'dag', 3600 => 'uur', 60 => 'min'] as $s => $l) {
        if ($d >= $s) {
            $n = (int) floor($d / $s);
            return "$n $l" . ($n > 1 && in_array($l, ['maand', 'week', 'dag'], true) ? 'en' : '') . ' geleden';
        }
    }
    return 'zojuist';
}

/** Rebase relative links/images inside README HTML onto the project folder. */
function readme_html(string $html, string $folder): string
{
    return preg_replace('~(href|src)="(?!https?://|//|/|#|mailto:|data:)~i', '$1="' . e($folder) . '/', $html);
}

require __DIR__ . '/views/index_view.php';
