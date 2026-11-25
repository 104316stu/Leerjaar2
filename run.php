<?php
/*
 * Runs a PHP page in a folder with a space in its name, see .htaccess.
 *
 * The page runs as if it was opened directly: same working directory, same
 * $_GET/$_POST. Apache has already checked the access rules (.htaccess) of the
 * address the visitor asked for, so only that exact address may be opened here.
 */

$_SERVER['SCRIPT_FILENAME'] = (function (): string {
    $path = rawurldecode((string) strtok($_SERVER['REQUEST_URI'] ?? '', '?'));
    if (str_ends_with($path, '/')) {
        $path .= 'index.php';
    }

    $file = __DIR__ . $path;
    $safe = str_contains($path, ' ')
        && str_ends_with($path, '.php')
        && !str_contains($path, chr(0))
        && !str_contains($path, chr(92)) // backslash
        && !str_contains($path, '//')
        && !str_contains($path, '/.') // no ".." and no hidden files
        && is_file($file)
        && str_starts_with((string) realpath($file), __DIR__ . DIRECTORY_SEPARATOR);

    if (!$safe) {
        require __DIR__ . '/404.php';
        exit;
    }

    $_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'] = $path;
    return $file;
})();

chdir(dirname($_SERVER['SCRIPT_FILENAME']));
require $_SERVER['SCRIPT_FILENAME'];
