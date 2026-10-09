<?php
require_once __DIR__ . '/config.php';

/** HTML-escape. */
function e($v): string
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

/** Format money; shows decimals only when needed. */
function money($amount): string
{
    $amount = (float)$amount;
    $decimals = abs($amount - round($amount)) > 0.00001 ? 2 : 0;
    return CURRENCY . ' ' . number_format($amount, $decimals);
}

// ---- CSRF ----------------------------------------------------------------
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(419);
        die('Your session expired or the form was invalid. Go back, refresh and try again.');
    }
}

function require_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        die('Method not allowed');
    }
    csrf_check();
}

// ---- Flash messages ------------------------------------------------------
function flash(string $type, string $msg): void
{
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}

function flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

// ---- Input helpers -------------------------------------------------------
function valid_date(?string $d): bool
{
    if (!$d || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
        return false;
    }
    [$y, $m, $day] = array_map('intval', explode('-', $d));
    return checkdate($m, $day, $y);
}

/** Returns a normalised decimal string or null when invalid / non-positive. */
function parse_amount($v): ?string
{
    if (!is_string($v) && !is_numeric($v)) {
        return null;
    }
    $v = str_replace(',', '', trim((string)$v));
    if (!preg_match('/^\d{1,10}(\.\d{1,2})?$/', $v) || (float)$v <= 0) {
        return null;
    }
    return number_format((float)$v, 2, '.', '');
}

function clean_text($v, int $max): string
{
    $v = trim(preg_replace('/\s+/u', ' ', (string)($v ?? '')));
    return mb_substr($v, 0, $max);
}

/** Find a lookup row by name (case-insensitive via collation) or create it. */
function find_or_create(PDO $conn, string $table, string $name): int
{
    if (!in_array($table, ['income_sources', 'expense_categories'], true)) {
        throw new InvalidArgumentException('bad table');
    }
    $stmt = $conn->prepare("SELECT id FROM $table WHERE name = ?");
    $stmt->execute([$name]);
    $id = $stmt->fetchColumn();
    if ($id) {
        return (int)$id;
    }
    $conn->prepare("INSERT INTO $table (name) VALUES (?)")->execute([$name]);
    return (int)$conn->lastInsertId();
}

/** Build a query string from the current GET, overriding some keys. */
function qs(array $override = []): string
{
    $q = array_merge($_GET, $override);
    $q = array_filter($q, fn($v) => $v !== '' && $v !== null);
    return $q ? '?' . http_build_query($q) : '';
}

/**
 * Parse shared list filters (from, to, search + one id filter) into SQL.
 * $dateCol and $searchCols are trusted constants from the caller.
 */
function list_filters(string $dateCol, string $idCol, string $idParam, array $searchCols): array
{
    $from = valid_date($_GET['from'] ?? '') ? $_GET['from'] : '';
    $to = valid_date($_GET['to'] ?? '') ? $_GET['to'] : '';
    $id = (int)($_GET[$idParam] ?? 0);
    $search = clean_text($_GET['search'] ?? '', 100);

    $where = [];
    $params = [];
    if ($from) { $where[] = "$dateCol >= ?"; $params[] = $from; }
    if ($to)   { $where[] = "$dateCol <= ?"; $params[] = $to; }
    if ($id)   { $where[] = "$idCol = ?";    $params[] = $id; }
    if ($search !== '') {
        $like = '%' . addcslashes($search, '%_\\') . '%';
        $where[] = '(' . implode(' OR ', array_map(fn($c) => "$c LIKE ?", $searchCols)) . ')';
        foreach ($searchCols as $_) { $params[] = $like; }
    }
    return [
        'from' => $from, 'to' => $to, 'id' => $id, 'search' => $search,
        'sql' => $where ? 'WHERE ' . implode(' AND ', $where) : '',
        'params' => $params,
    ];
}

function send_csv(string $filename, array $header, iterable $rows): never
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel reads UTF-8
    fputcsv($out, $header);
    foreach ($rows as $r) {
        // Neutralise spreadsheet formula injection.
        $r = array_map(fn($c) => is_string($c) && preg_match('/^[=+\-@\t\r]/', $c) ? "'" . $c : $c, $r);
        fputcsv($out, $r);
    }
    fclose($out);
    exit;
}
