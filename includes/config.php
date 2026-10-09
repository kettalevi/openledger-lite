<?php
/**
 * OpenLedger Lite configuration.
 *
 * Defaults can be overridden by environment variables (OL_DB_HOST, OL_DB_NAME,
 * OL_DB_USER, OL_DB_PASS, OL_CURRENCY, OL_BASE_URL) or by creating
 * includes/config.local.php (git-ignored) that calls define() for any constant.
 */
if (is_file(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

function ol_env(string $key, string $default): string
{
    $v = getenv($key);
    return $v === false ? $default : $v;
}

defined('DB_HOST')  || define('DB_HOST', ol_env('OL_DB_HOST', 'localhost'));
defined('DB_NAME')  || define('DB_NAME', ol_env('OL_DB_NAME', 'openledger_lite'));
defined('DB_USER')  || define('DB_USER', ol_env('OL_DB_USER', 'root'));
defined('DB_PASS')  || define('DB_PASS', ol_env('OL_DB_PASS', ''));
defined('CURRENCY') || define('CURRENCY', ol_env('OL_CURRENCY', 'UGX'));
defined('PER_PAGE') || define('PER_PAGE', 25);

// BASE_URL: web path of the app root, e.g. "" or "/openledger-lite" (no trailing slash).
if (!defined('BASE_URL')) {
    $env = getenv('OL_BASE_URL');
    if ($env !== false) {
        define('BASE_URL', rtrim($env, '/'));
    } else {
        $root = str_replace('\\', '/', realpath(__DIR__ . '/..'));
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']) ?: '') : '';
        $base = ($docRoot !== '' && strpos($root, $docRoot) === 0) ? substr($root, strlen($docRoot)) : '';
        define('BASE_URL', rtrim($base, '/'));
    }
}

if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => BASE_URL === '' ? '/' : BASE_URL,
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_name('openledger');
    session_start();
}
