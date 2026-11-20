<?php
/*
 * Starts the GitHub deploy workflow when a linked repo has new commits.
 *
 * Called by deploy-check.php (an outside timer, every few minutes) and, as a
 * fallback, after a visitor has received the home page.
 */

require_once __DIR__ . '/projects.php';

function deploy_config(): ?array
{
    $file = __DIR__ . '/config.local.php';
    $cfg = is_file($file) ? require $file : null;
    return is_array($cfg) && !empty($cfg['token']) ? $cfg : null;
}

/**
 * Compare each linked repo's last push with the one seen before.
 * Returns what happened, for the log of deploy-check.php.
 *
 * @param int $minGap  seconds between two GitHub checks
 */
function deploy_check(string $root, int $minGap = 60): array
{
    $cfg = deploy_config();
    if (!$cfg) {
        return ['status' => 'off', 'reason' => 'geen config.local.php'];
    }

    $stateFile = $root . '/cache/deploy-state.json';
    $lock = fopen($stateFile . '.lock', 'c');
    if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
        return ['status' => 'busy'];
    }

    try {
        return deploy_check_locked($root, $stateFile, $cfg, $minGap);
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}

function deploy_check_locked(string $root, string $stateFile, array $cfg, int $minGap): array
{
    $state = is_file($stateFile) ? (json_decode((string) file_get_contents($stateFile), true) ?: []) : [];
    if (time() - (int) ($state['checked'] ?? 0) < $minGap) {
        return ['status' => 'skipped', 'reason' => 'net nog gecontroleerd'];
    }
    $state['checked'] = time();

    $headers = [
        'Authorization: Bearer ' . $cfg['token'],
        'Accept: application/vnd.github+json',
        'X-GitHub-Api-Version: 2022-11-28',
        'User-Agent: leerjaar2-deploy-check',
    ];

    $seen = $state['seen'] ?? [];
    $new = $seen;
    $changed = [];
    foreach (linked_repos($root) as $slug) {
        $ch = curl_init("https://api.github.com/repos/$slug");
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5, CURLOPT_HTTPHEADER => $headers]);
        $data = json_decode((string) curl_exec($ch), true);
        curl_close($ch);
        if (!empty($data['pushed_at'])) {
            $new[$slug] = $data['pushed_at'];
            if (($seen[$slug] ?? null) !== $data['pushed_at']) {
                $changed[] = $slug;
            }
        }
    }

    $result = ['status' => 'unchanged', 'repos' => array_keys($new)];
    if ($changed) {
        $ch = curl_init("https://api.github.com/repos/{$cfg['repo']}/actions/workflows/{$cfg['file']}/dispatches");
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode(['ref' => $cfg['ref']]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_HTTPHEADER     => array_merge($headers, ['Content-Type: application/json']),
        ]);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code === 204) {
            $state['seen'] = $new;
            $result = ['status' => 'deploy started', 'changed' => $changed];
        } else {
            $result = ['status' => 'error', 'reason' => "GitHub gaf HTTP $code", 'changed' => $changed];
        }
    }

    @file_put_contents($stateFile, json_encode($state));
    return $result;
}
