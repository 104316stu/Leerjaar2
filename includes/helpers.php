<?php
/* Small helpers shared by the pages. */

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** "3 dagen geleden" style time from an ISO date or unix time. */
function ago($when): string
{
    if (!$when) {
        return '';
    }
    $d = time() - (is_int($when) ? $when : strtotime($when));
    foreach ([31536000 => 'jaar', 2592000 => 'maand', 604800 => 'week', 86400 => 'dag', 3600 => 'uur', 60 => 'min'] as $s => $l) {
        if ($d >= $s) {
            $n = (int) floor($d / $s);
            return "$n $l" . ($n > 1 && in_array($l, ['maand', 'week', 'dag'], true) ? 'en' : '') . ' geleden';
        }
    }
    return 'zojuist';
}

/** Link to a file in this site with its change time, so browsers load a new version right away. */
function asset(string $path): string
{
    $file = dirname(__DIR__) . '/' . $path;
    $v = is_file($file) ? filemtime($file) : 0;
    return $path . '?v=' . $v;
}

/** Encode each part of a folder path for use in a link ("Oefening 2.2" -> "Oefening%202.2"). */
function url_path(string $path): string
{
    return implode('/', array_map('rawurlencode', explode('/', $path)));
}

/** Turn a folder or repo name into a readable title: "ontkoking-" -> "Ontkoking". */
function pretty_title(string $name): string
{
    $name = trim(str_replace(['_', '-'], ' ', $name));
    $name = preg_replace('/\s+/', ' ', $name);
    return $name === '' ? '' : mb_strtoupper(mb_substr($name, 0, 1)) . mb_substr($name, 1);
}

/** Rebase relative links/images inside README HTML onto the project folder. */
function readme_html(string $html, string $folder): string
{
    return preg_replace('~(href|src)="(?!https?://|//|/|#|mailto:|data:)~i', '$1="' . e(url_path($folder)) . '/', $html);
}
