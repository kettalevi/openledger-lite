<?php
require_once '../includes/db.php';
$current = 'income';
$pageTitle = 'Income';
require_once '../includes/header.php';

$sources = $conn->query('SELECT id, name FROM income_sources ORDER BY name')->fetchAll();

$f = list_filters('i.income_date', 'i.source_id', 'source_id', ['i.contributor_name', 's.name']);
$from = $f['from']; $to = $f['to']; $source_id = $f['id']; $search = $f['search'];
$from_sql = "FROM income i LEFT JOIN income_sources s ON i.source_id = s.id {$f['sql']}";

$stmt = $conn->prepare("SELECT COUNT(*), COALESCE(SUM(i.amount), 0) $from_sql");
$stmt->execute($f['params']);
[$count, $totalIncome] = $stmt->fetch(PDO::FETCH_NUM);

$pages = max(1, (int)ceil($count / PER_PAGE));
$page = min(max(1, (int)($_GET['page'] ?? 1)), $pages);
$offset = ($page - 1) * PER_PAGE;

$stmt = $conn->prepare("
    SELECT i.id, i.income_date, i.amount, i.contributor_name, s.name AS source_name
    $from_sql
    ORDER BY i.income_date DESC, i.id DESC
    LIMIT " . PER_PAGE . " OFFSET $offset
");
$stmt->execute($f['params']);
$rows = $stmt->fetchAll();
?>

<div class="flex flex-wrap justify-between items-center gap-2 mb-6">
    <h2 class="text-2xl font-bold">Income Records</h2>
    <div class="flex gap-2">
        <a href="<?= e(url('exports/income_csv.php') . qs(['page' => null])) ?>"
           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Export CSV</a>
        <a href="create.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Add Income</a>
    </div>
</div>

<form method="GET" class="bg-white p-4 rounded shadow mb-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
    <input type="date" name="from" value="<?= e($from) ?>" title="From" class="border p-2 rounded">
    <input type="date" name="to" value="<?= e($to) ?>" title="To" class="border p-2 rounded">
    <select name="source_id" class="border p-2 rounded">
        <option value="">All Sources</option>
        <?php foreach ($sources as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= $source_id == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search..." class="border p-2 rounded">
    <div class="flex gap-2">
        <button class="bg-gray-800 text-white px-4 py-2 rounded flex-1">Filter</button>
        <a href="index.php" class="px-4 py-2 border rounded text-center">Reset</a>
    </div>
</form>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-gray-500">Total Income</h3>
        <p class="text-xl font-bold text-green-600"><?= e(money($totalIncome)) ?></p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-gray-500">Entries</h3>
        <p class="text-xl font-bold"><?= (int)$count ?></p>
    </div>
</div>

<div class="bg-white p-4 rounded shadow overflow-x-auto">
<table class="w-full table-auto">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Date</th>
            <th class="p-2 text-left">Source</th>
            <th class="p-2 text-left">Contributor</th>
            <th class="p-2 text-right">Amount</th>
            <th class="p-2 text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $r): ?>
        <tr class="border-b">
            <td class="p-2 whitespace-nowrap"><?= e($r['income_date']) ?></td>
            <td class="p-2"><?= e($r['source_name']) ?></td>
            <td class="p-2"><?= e($r['contributor_name']) ?></td>
            <td class="p-2 text-right text-green-600 font-bold whitespace-nowrap"><?= e(money($r['amount'])) ?></td>
            <td class="p-2 text-right whitespace-nowrap">
                <a href="edit.php?id=<?= (int)$r['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                <form method="POST" action="delete.php" class="inline"
                      onsubmit="return confirm('Delete this income entry?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <button class="text-red-600 hover:underline ml-2">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
        <tr><td colspan="5" class="p-6 text-center text-gray-500">No income records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<?php require '../includes/pagination.php'; ?>
<?php require_once '../includes/footer.php'; ?>
