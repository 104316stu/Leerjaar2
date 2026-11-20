<?php
/*
 * Called every few minutes by an outside timer (for example cron-job.org):
 *   https://104316.stu.sd-lab.nl/deploy-check.php?key=<DEPLOY_CHECK_KEY>
 * Starts the deploy when a linked GitHub repo has new commits.
 * Without the right key it acts as if the page does not exist.
 */

require __DIR__ . '/includes/helpers.php';
require __DIR__ . '/includes/deploy.php';

$cfg = deploy_config();
$key = (string) ($_GET['key'] ?? '');

if (!$cfg || empty($cfg['check_key']) || !hash_equals((string) $cfg['check_key'], $key)) {
    http_response_code(404);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo json_encode(deploy_check(__DIR__, 30), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
