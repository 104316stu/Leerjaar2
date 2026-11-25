<?php
/*
 * Finds every project in the site and collects what the page shows about it.
 *
 * A folder is a project when it holds one of:
 *   - <name>.git     a GitHub URL; the deploy copies that repo into the folder
 *   - project.json   title, description, cover etc. written by hand (see readme.md)
 *   - index.php / index.html
 * A project inside another project's folder belongs to that project and is not listed separately.
 */

require_once __DIR__ . '/github.php';

const SCAN_SKIP  = ['.git', '.github', 'cache', 'includes', 'assets', 'views', 'node_modules', 'vendor', '.idea', '.vscode'];
const SCAN_DEPTH = 4;
const NEW_FOR    = 172800; // a project counts as "nieuw" for 48 hours after a change

/** folder => ['git' => path|null, 'manifest' => path|null, 'index' => bool] */
function find_project_folders(string $root): array
{
    $it = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
            fn ($f) => !($f->isDir() && in_array($f->getFilename(), SCAN_SKIP, true))
        ),
        RecursiveIteratorIterator::SELF_FIRST
    );
    $it->setMaxDepth(SCAN_DEPTH);

    $found = [];
    foreach ($it as $file) {
        if ($file->isDir()) {
            continue;
        }
        $rel  = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
        $dir  = dirname($rel);
        $name = $file->getFilename();
        if ($dir === '.') {
            continue;
        }
        $found[$dir] ??= ['git' => null, 'manifest' => null, 'index' => false];
        if (str_ends_with($name, '.git')) {
            $found[$dir]['git'] = $file->getPathname();
        } elseif ($name === 'project.json') {
            $found[$dir]['manifest'] = $file->getPathname();
        } elseif ($name === 'index.php' || $name === 'index.html') {
            $found[$dir]['index'] = true;
        } else {
            continue;
        }
    }

    // Keep only folders with a marker, and drop folders that sit inside another project.
    $found = array_filter($found, fn ($f) => $f['git'] || $f['manifest'] || $f['index']);
    ksort($found);
    $result = [];
    foreach ($found as $dir => $f) {
        foreach ($result as $parent => $_) {
            if (str_starts_with($dir, $parent . '/')) {
                continue 2;
            }
        }
        $result[$dir] = $f;
    }
    return $result;
}

/** GitHub repos linked with a *.git file, as "owner/repo" slugs. */
function linked_repos(string $root): array
{
    $slugs = [];
    foreach (find_project_folders($root) as $f) {
        if ($f['git'] && ($gh = github_parse_git_file($f['git']))) {
            $slugs[] = strtolower($gh['owner'] . '/' . $gh['repo']);
        }
    }
    return array_values(array_unique($slugs));
}

/** Info about the last deploy, written by the GitHub workflow. */
function load_build(string $root): ?array
{
    $file = $root . '/cache/build.json';
    $data = is_file($file) ? json_decode((string) file_get_contents($file), true) : null;
    return is_array($data) ? $data : null;
}

/** A link inside a project folder, or an outside link as it is. */
function project_link(string $folder, string $target): string
{
    if (preg_match('~^(https?:)?//~i', $target)) {
        return $target;
    }
    return url_path($folder) . '/' . url_path(ltrim($target, './'));
}

/** Newest change time of the files in a folder (used when nothing better is known). */
function folder_mtime(string $dir): ?int
{
    $newest = null;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        $newest = max($newest ?? 0, $file->getMTime());
    }
    return $newest;
}

function load_projects(string $root, ?array $build): array
{
    $dataFile  = $root . '/cache/github-data.json';
    $static    = is_file($dataFile) ? (json_decode((string) file_get_contents($dataFile), true) ?: []) : [];
    $projects  = [];

    foreach (find_project_folders($root) as $folder => $f) {
        $m = [];
        if ($f['manifest']) {
            $m = json_decode((string) file_get_contents($f['manifest']), true);
            if (!is_array($m)) {
                $m = ['fout' => 'project.json is geen geldige JSON'];
            }
        }
        if (!empty($m['verberg'])) {
            continue;
        }

        // GitHub data: prepared by the deploy, or fetched live until the first deploy has run.
        $gh = $f['git'] ? github_parse_git_file($f['git']) : null;
        $repo = null;
        $readme = '';
        if ($gh) {
            $key = strtolower($gh['owner'] . '/' . $gh['repo']);
            if (!empty($static[$key]['repo'])) {
                $repo   = $static[$key]['repo'];
                $readme = $static[$key]['readme'] ?? '';
            } else {
                $repo   = github_repo($gh['owner'], $gh['repo'])['data'];
                $readme = (string) (github_readme($gh['owner'], $gh['repo'])['data'] ?? '');
            }
        }

        $slug = trim(strtolower(preg_replace('/[^a-z0-9]+/i', '-', $folder)), '-');

        // Link: "link" from project.json, otherwise the folder itself when it has a page.
        $link = null;
        if (!empty($m['link'])) {
            $link = project_link($folder, $m['link']);
        } elseif ($f['index'] || $gh) {
            $link = url_path($folder) . '/';
        }

        // Cover: one set in project.json, otherwise a screenshot made by the deploy.
        $cover = null;
        $autoCover = false;
        if (!empty($m['cover'])) {
            $cover = project_link($folder, $m['cover']);
        } else {
            foreach (['webp', 'jpg', 'png'] as $ext) {
                $shot = "assets/covers/$slug.$ext";
                if (is_file("$root/$shot")) {
                    $cover = $shot . '?v=' . filemtime("$root/$shot");
                    $autoCover = true;
                    break;
                }
            }
        }

        // Last change: from the deploy (git history), else GitHub, else file times.
        $updated = $build['folders'][$folder] ?? ($repo['pushed_at'] ?? null);
        $updatedTs = $updated ? strtotime($updated) : folder_mtime("$root/$folder");

        $parts = [];
        foreach ((array) ($m['onderdelen'] ?? []) as $part) {
            if (!empty($part['titel']) && !empty($part['link'])) {
                $parts[] = ['title' => $part['titel'], 'link' => project_link($folder, $part['link'])];
            }
        }

        $title = $m['titel'] ?? pretty_title($repo['name'] ?? basename($folder));
        $projects[] = [
            'slug'        => $slug,
            'folder'      => $folder,
            'subject'     => $m['vak'] ?? explode('/', $folder)[0],
            'title'       => $title,
            'description' => $m['beschrijving'] ?? ($repo['description'] ?? ''),
            'tags'        => array_values((array) ($m['tags'] ?? ($repo['topics'] ?? []))),
            'language'    => $m['taal'] ?? ($repo['language'] ?? ''),
            'period'      => $m['periode'] ?? '',
            'status'      => $m['status'] ?? '',
            'featured'    => !empty($m['uitgelicht']),
            'link'        => $link,
            'has_page'    => $f['index'] || $gh, // otherwise its folder can be looked into, see layers.php
            'cover'       => $cover,
            'auto_cover'  => $autoCover,
            'needs_shot'  => $link && empty($m['cover']) && !str_starts_with($link, 'http'),
            'parts'       => $parts,
            'github'      => $gh['url'] ?? null,
            'readme'      => $readme,
            'updated'     => $updatedTs,
            'is_new'      => $updatedTs && time() - $updatedTs < NEW_FOR,
            'error'       => $m['fout'] ?? null,
            'search'      => mb_strtolower(implode(' ', [
                $title, $folder, $m['beschrijving'] ?? ($repo['description'] ?? ''),
                implode(' ', (array) ($m['tags'] ?? [])), $m['taal'] ?? ($repo['language'] ?? ''),
            ])),
        ];
    }

    return $projects;
}

/** Projects per vak, in the order of includes/subjects.php; newest project first. */
function group_by_subject(array $projects): array
{
    $names = require __DIR__ . '/subjects.php';
    $order = array_flip(array_keys($names));

    $groups = [];
    foreach ($projects as $p) {
        $key = $p['subject'];
        $groups[$key] ??= ['key' => $key, 'name' => $names[$key] ?? pretty_title($key), 'projects' => []];
        $groups[$key]['projects'][] = $p;
    }
    uksort($groups, fn ($a, $b) => [$order[$a] ?? 99, $a] <=> [$order[$b] ?? 99, $b]);

    $nr = 0;
    foreach ($groups as &$g) {
        $g['nr'] = sprintf('%02d', ++$nr);
        usort($g['projects'], fn ($a, $b) => [$b['updated'] ?? 0, $a['title']] <=> [$a['updated'] ?? 0, $b['title']]);
    }
    unset($g);
    return array_values($groups);
}
