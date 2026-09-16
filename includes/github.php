<?php
/**
 * Small GitHub REST API client with ETag-based conditional requests.
 *
 * Every response is cached in cache/<key>.json together with its ETag.
 * On the next call the ETag is sent back as If-None-Match; when GitHub
 * answers 304 Not Modified we keep serving the cached data and the
 * request does not count against the rate limit.
 */

const GITHUB_API   = 'https://api.github.com';
const GITHUB_CACHE = __DIR__ . '/../cache';
const GITHUB_UA    = 'Leerjaar2-portfolio (PHP cURL)';

/**
 * Parse a *.git file containing a GitHub URL into [owner, repo].
 * Returns null when the file does not contain a GitHub URL.
 */
function github_parse_git_file(string $path): ?array
{
    $url = trim((string) @file_get_contents($path));
    if ($url === '') {
        return null;
    }
    if (!preg_match('~github\.com[/:]([^/\s]+)/([^/\s]+?)(?:\.git)?/?$~i', $url, $m)) {
        return null;
    }
    return ['owner' => $m[1], 'repo' => $m[2], 'url' => "https://github.com/{$m[1]}/{$m[2]}"];
}

function github_cache_path(string $key): string
{
    return GITHUB_CACHE . '/' . preg_replace('/[^a-z0-9._-]+/i', '_', $key) . '.json';
}

function github_cache_read(string $key): ?array
{
    $file = github_cache_path($key);
    if (!is_file($file)) {
        return null;
    }
    $data = json_decode((string) file_get_contents($file), true);
    return is_array($data) ? $data : null;
}

function github_cache_write(string $key, array $entry): void
{
    if (!is_dir(GITHUB_CACHE)) {
        @mkdir(GITHUB_CACHE, 0755, true);
    }
    file_put_contents(github_cache_path($key), json_encode($entry, JSON_PRETTY_PRINT), LOCK_EX);
}

/**
 * Perform a conditional GET against the GitHub API.
 *
 * @param string $endpoint  e.g. "/repos/owner/name"
 * @param string $key       cache key
 * @param string $accept    Accept header (json or html)
 * @return array{data:mixed,status:int,from_cache:bool,fetched_at:int,error:?string}
 */
function github_get(string $endpoint, string $key, string $accept = 'application/vnd.github+json'): array
{
    $cached = github_cache_read($key);

    $headers = [
        'Accept: ' . $accept,
        'User-Agent: ' . GITHUB_UA,
        'X-GitHub-Api-Version: 2022-11-28',
    ];
    if ($cached && !empty($cached['etag'])) {
        $headers[] = 'If-None-Match: ' . $cached['etag'];
    }

    $ch = curl_init(GITHUB_API . $endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $response = curl_exec($ch);
    $curlErr  = curl_error($ch);
    $status   = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $headerSz = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);

    $result = [
        'data'       => $cached['data'] ?? null,
        'status'     => $status,
        'from_cache' => true,
        'fetched_at' => $cached['fetched_at'] ?? 0,
        'error'      => null,
    ];

    // Network failure: fall back to cache if we have one.
    if ($response === false) {
        $result['error'] = $cached ? null : 'cURL error: ' . $curlErr;
        return $result;
    }

    $rawHeaders = substr($response, 0, $headerSz);
    $body       = substr($response, $headerSz);

    // 304: nothing changed, refresh the "checked" timestamp and serve cache.
    if ($status === 304 && $cached) {
        $cached['checked_at'] = time();
        github_cache_write($key, $cached);
        return $result;
    }

    if ($status === 200) {
        $etag = null;
        if (preg_match('/^etag:\s*(.+)$/mi', $rawHeaders, $m)) {
            $etag = trim($m[1]);
        }
        $data = str_starts_with($accept, 'application/vnd.github.html')
            ? $body
            : json_decode($body, true);

        github_cache_write($key, [
            'etag'       => $etag,
            'fetched_at' => time(),
            'checked_at' => time(),
            'endpoint'   => $endpoint,
            'data'       => $data,
        ]);

        return ['data' => $data, 'status' => 200, 'from_cache' => false, 'fetched_at' => time(), 'error' => null];
    }

    // 403/429 rate limit, 404, 5xx… keep serving cache if possible.
    $msg = "GitHub API returned HTTP $status";
    if ($status === 403 || $status === 429) {
        $msg .= ' (rate limit reached)';
        if (preg_match('/^x-ratelimit-reset:\s*(\d+)/mi', $rawHeaders, $m)) {
            $msg .= ', resets at ' . date('H:i', (int) $m[1]);
        }
    }
    $result['error'] = $cached ? null : $msg;
    return $result;
}

/** Repository metadata (name, description, stars, forks, pushed_at, …). */
function github_repo(string $owner, string $repo): array
{
    return github_get("/repos/$owner/$repo", "$owner-$repo-repo");
}

/** README rendered to HTML by GitHub. */
function github_readme(string $owner, string $repo): array
{
    return github_get("/repos/$owner/$repo/readme", "$owner-$repo-readme", 'application/vnd.github.html');
}
