<?php
/*
 * Turns the flat list of projects into layers: vak > map > project.
 * Every layer has its own page, index.php?vak=<key>, for example ?vak=Program2/OOP.
 *
 * A project without a page of its own (only a project.json) can be looked into:
 * its subfolders are layers too, with the pages and documents found there.
 */

require_once __DIR__ . '/projects.php';

// Only these files are listed; databases, config and source code never are.
const LAYER_IMAGES = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'avif'];
const LAYER_DOCS   = ['pdf', 'docx', 'pptx', 'xlsx', 'mp4', 'mp3'];
const LAYER_DEPTH  = 5;

function new_layer(string $kind, string $key, string $title, array $trail): array
{
    return [
        'kind' => $kind, 'key' => $key, 'title' => $title, 'trail' => $trail,
        'folders' => [], 'projects' => [], 'pages' => [], 'files' => [], 'project' => null,
    ];
}

/** Where a project sits: its folder, or directly under the vak chosen in project.json. */
function layer_key(array $p): string
{
    return str_starts_with($p['folder'], $p['subject'] . '/')
        ? $p['folder']
        : $p['subject'] . '/' . basename($p['folder']);
}

/** All layers, as key => layer. 'trail', 'folders' and 'projects' hold keys of other layers. */
function build_layers(string $root, array $groups): array
{
    $layers = [];
    foreach ($groups as $g) {
        $layers[$g['key']] = new_layer('vak', $g['key'], $g['name'], []);

        foreach ($g['projects'] as $p) {
            $key     = layer_key($p);
            $between = array_slice(explode('/', substr($key, strlen($g['key']) + 1)), 0, -1);

            // The folders between the vak and the project.
            $trail = [$g['key']];
            foreach ($between as $name) {
                $up     = end($trail);
                $folder = "$up/$name";
                $layers[$folder] ??= new_layer('map', $folder, pretty_title($name), $trail);
                if (!in_array($folder, $layers[$up]['folders'], true)) {
                    $layers[$up]['folders'][] = $folder;
                }
                $trail[] = $folder;
            }

            $layers[end($trail)]['projects'][] = $key;
            $layers[$key] = new_layer('project', $key, $p['title'], $trail);
            $layers[$key]['project'] = $p;
            $layers[$key]['pages']   = $p['parts'];
            if (!$p['has_page']) {
                add_contents($layers, $root, $key, $p['folder'], array_column($p['parts'], 'link'));
            }
        }
    }

    // Natural order, so "Opdracht 2" comes before "Opdracht 10".
    $byTitle = fn ($a, $b) => strnatcasecmp($layers[$a]['title'], $layers[$b]['title']);
    foreach (array_keys($layers) as $key) {
        usort($layers[$key]['folders'], $byTitle);
        usort($layers[$key]['projects'], $byTitle);
    }
    return $layers;
}

/** Adds the pages, documents and subfolders of $folder to the layer $key. */
function add_contents(array &$layers, string $root, string $key, string $folder, array $skipLinks, int $depth = 0): void
{
    foreach (visible_names("$root/$folder") as $name) {
        $rel = "$folder/$name";
        $abs = "$root/$rel";

        if (is_dir($abs)) {
            if (in_array($name, SCAN_SKIP, true) || is_link($abs) || $depth >= LAYER_DEPTH) {
                continue;
            }
            $page = page_in($abs, $rel);
            if ($page !== null) {
                if (!in_array($page, $skipLinks, true)) {
                    $layers[$key]['pages'][] = ['title' => pretty_title($name), 'link' => $page];
                }
                continue;
            }
            $sub = "$key/$name";
            $layers[$sub] = new_layer('map', $sub, pretty_title($name), [...$layers[$key]['trail'], $key]);
            add_contents($layers, $root, $sub, $rel, $skipLinks, $depth + 1);
            if ($layers[$sub]['folders'] || $layers[$sub]['pages'] || $layers[$sub]['files']) {
                $layers[$key]['folders'][] = $sub;
            } else {
                unset($layers[$sub]);
            }
            continue;
        }

        $ext   = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $image = in_array($ext, LAYER_IMAGES, true);
        $link  = url_path($rel);
        if ((!$image && !in_array($ext, LAYER_DOCS, true)) || in_array($link, $skipLinks, true)) {
            continue;
        }
        $layers[$key]['files'][] = ['title' => $name, 'link' => $link, 'ext' => $ext, 'image' => $image];
    }
}

/** Link to the page of a folder: its index file, or the only .html file in it. */
function page_in(string $abs, string $rel): ?string
{
    if (is_file("$abs/index.php") || is_file("$abs/index.html")) {
        return url_path($rel) . '/';
    }
    $html = array_values(array_filter(visible_names($abs), fn ($n) => str_ends_with(strtolower($n), '.html')));
    return count($html) === 1 ? url_path($rel) . '/' . rawurlencode($html[0]) : null;
}

/** Names in a folder in natural order, without hidden ones. */
function visible_names(string $abs): array
{
    $names = array_filter(scandir($abs) ?: [], fn ($n) => $n[0] !== '.');
    usort($names, 'strnatcasecmp');
    return $names;
}

/** Link to the page of a layer. */
function layer_url(string $key): string
{
    return './?vak=' . url_path($key);
}

/** Titles of what is directly inside a layer. */
function layer_contents(array $layers, array $layer): array
{
    return [
        ...array_map(fn ($k) => $layers[$k]['title'], [...$layer['folders'], ...$layer['projects']]),
        ...array_column($layer['pages'], 'title'),
        ...array_column($layer['files'], 'title'),
    ];
}

/** Where a project card leads: the live project, or its layer when it has no page of its own. */
function project_href(array $layer): string
{
    $p = $layer['project'];
    return $p['has_page'] && $p['link'] ? $p['link'] : layer_url($layer['key']);
}

/** Everything the search box can find. */
function search_index(array $layers): array
{
    $items = [];
    foreach ($layers as $layer) {
        $path = implode(' / ', array_map(fn ($k) => $layers[$k]['title'], $layer['trail']));
        $p    = $layer['project'];
        $items[] = [
            'title'   => $layer['title'],
            'path'    => $path,
            'href'    => $p ? project_href($layer) : layer_url($layer['key']),
            'search'  => mb_strtolower("{$layer['title']} $path {$layer['key']} " . ($p['search'] ?? '')),
            'project' => $p,
        ];
        $inside = ltrim("$path / {$layer['title']}", ' /');
        foreach ([...$layer['pages'], ...$layer['files']] as $item) {
            $items[] = [
                'title'   => $item['title'],
                'path'    => $inside,
                'href'    => $item['link'],
                'search'  => mb_strtolower("{$item['title']} $inside"),
                'project' => null,
            ];
        }
    }
    return $items;
}
